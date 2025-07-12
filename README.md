# BeatLink

Bachelor's degree project.

## Overview

BeatLink is a web-based music platform developed as part of a bachelor's degree project at the University "Alexandru Ioan Cuza" of Iasi, Faculty of Computer Science. It leverages PHP and Blade for backend logic and templating, while JavaScript and CSS provide an engaging and responsive user experience. BeatLink integrates advanced audio processing and interactive features to deliver a unique music discovery experience.

## Features

- **User Registration & Authentication**: Secure sign-up and login system.
- **Music Sharing**: Upload and share music tracks.
- **Recommendation Algorithm**: Discover new tracks through our intelligent recommendation system based on your listening habits.
- **Sound Analyzer Engine (Essentia)**: Analyze tracks using the Essentia engine to extract detailed audio features.
- **Real Time Chat**: Engage in real-time communication with other users.
- **Responsive Design**: Optimized for desktops, tablets, and mobile devices.

## Demo Video

Watch our demo video to get a quick overview of how BeatLink works:

[![Demo Video](https://youtu.be/utSL46Bzbj0)


## Technologies Used

- **PHP (45.1%)**: Backbone of the server-side logic and APIs.
- **Blade (30.4%)**: Templating engine for dynamic front-end rendering.
- **JavaScript (16.3%)**: Powers interactive features and client-side logic.
- **CSS (7.5%)**: Styles the platform, ensuring a modern and responsive design.
- **Other (0.7%)**: Miscellaneous scripts and assets.

## Getting Started

### Prerequisites

- PHP version 8.0 or higher
- Composer for dependency management
- Node.js and npm for frontend dependencies
- A web server (e.g., Apache, Nginx)
- MySQL or a compatible relational database

### Installation

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/Spaghedi3/BeatLink.git
   cd BeatLink
   ```

2. **Install PHP Dependencies:**
   ```bash
   composer install
   ```

3. **Install Frontend Dependencies:**
   ```bash
   npm install
   ```

4. **Configure Environment Variables:**
   - Copy the `.env.example` file to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Edit the `.env` file to set your database connection and other configuration details.

5. **Run Database Migrations:**
   ```bash
   php artisan migrate
   ```

6. **Start the Development Server:**
   ```bash
   php artisan serve
   ```

## Usage

- Access the application by navigating to `http://localhost:8000` (or your configured domain).
- Create an account or log in to your existing account.
- Enjoy music sharing, intelligent recommendations, advanced sound analysis via Essentia, and real-time chat with fellow users.

## License

*Note:* The project does not use the MIT License. For clarification on the licensing terms and any usage rights, please contact the repository owner.

## Contributing

Contributions are welcome! Please fork the repository, create a new branch with your changes, and submit a pull request. Ensure your code adheres to the project's coding standards and includes appropriate tests.

## Contact

For any questions or issues, please open an issue on GitHub or contact the repository owner, [Spaghedi3](https://github.com/Spaghedi3).
