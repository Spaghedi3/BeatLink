import pandas as pd
from surprise import Dataset, Reader, SVD
from surprise.model_selection import train_test_split
from surprise import accuracy
from collections import defaultdict
import csv

# === Step 1: Load hybrid dataset ===
df = pd.read_csv(r'C:\Users\edyed\Documents\LICENTA\BeatLink\BeatLink\BeatLink\storage\app\public\hybrid_dataset.csv')

# Ensure consistent column naming
df.rename(columns={'beat_id': 'track_id'}, inplace=True)

reader = Reader(rating_scale=(-1, 2))  # Hybrid ratings: -1 (bad) to 2 (very positive)
data = Dataset.load_from_df(df[['user_id', 'track_id', 'rating']], reader)

# === Step 2: Train SVD model ===
trainset, testset = train_test_split(data, test_size=0.2)
model = SVD()
model.fit(trainset)

# === Step 3: Generate top-N recommendations ===
def get_top_n(predictions, n=10):
    top_n = defaultdict(list)
    for uid, iid, true_r, est, _ in predictions:
        top_n[uid].append((iid, est))

    for uid in top_n:
        top_n[uid] = sorted(top_n[uid], key=lambda x: x[1], reverse=True)[:n]

    return top_n

# Predict for all user-track pairs not in trainset
anti_testset = trainset.build_anti_testset()
predictions = model.test(anti_testset)
top_n = get_top_n(predictions, n=10)

# === Step 4: Save recommendations ===  
output_file = 'storage/app/recommendations.csv'
with open(output_file, 'w', newline='') as csvfile:
    writer = csv.writer(csvfile)
    writer.writerow(['user_id', 'track_id', 'score'])
    for uid, user_recs in top_n.items():
        for iid, score in user_recs:
            writer.writerow([uid, iid, round(score, 3)])

print(f"Top recommendations saved to {output_file}")
