import sys
import essentia.standard as es

audio_file = sys.argv[1]

loader = es.MonoLoader(filename=audio_file)
audio = loader()

# Tempo
rhythm_extractor = es.RhythmExtractor2013(method="multifeature")
bpm, _, _, _, _ = rhythm_extractor(audio)

# Key
key_extractor = es.KeyExtractor()
key, scale, _ = key_extractor(audio)

# Output clean values
print(f"{int(round(bpm))}")
print(f"Key: {key} {scale}")
