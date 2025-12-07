# 🚀 Student Facility Booking System

This is a web application designed to **streamline the process of booking university facilities** (such as discussion rooms, nap pads, and activity center) by students. It provides a real-time view of availability, enforces booking policies (like time slots and daily limits), and offers an administration/library staff panel for facility management.

The system is built on the **Laravel 10+ framework**, providing a robust, secure, and maintainable structure.

-----

## ✨ Key Features

### For Students

  * **Real-time Availability:** View facility availability broken down into specific time slots (e.g., 8:00 AM - 9:00 AM).
  * **Instant Booking:** Select an available slot and confirm a booking through a simple modal interface.
  * **Dynamic Status:** Time slots are clearly marked as **Available**, **Full**, or **Passed** (if the slot time has already gone).
  * **Personal Dashboard:** View all current and past bookings.
  * **Cancellation:** Easily cancel pending bookings.

### For Administrators

  * **Facility Management:** Create, update, and delete facility records (capacity, type, status, name).
  * **Booking Oversight:** View all current and future bookings across all facilities.
  * **User Management (Basic):** Manage student accounts and access levels (if implemented).

-----

## 🛠️ Technologies Used

| Category | Technology | Description |
| :--- | :--- | :--- |
| **Backend** | PHP (\>= 8.1) | The primary server-side scripting language. |
| **Framework** | Laravel 10+ | Robust MVC framework for system architecture. |
| **Database** | MySQL / SQLite | Data persistence for facilities, users, and bookings. |
| **Frontend** | Blade Templates | Laravel's templating engine. |
| **Styling** | **Tailwind CSS** | Utility-first CSS framework for responsive design. |
| **State** | Session Management | Handling user login state and flash messages. |

-----

## ⚙️ Installation and Setup

Follow these steps to get a local copy of the project running.

### Prerequisites

  * PHP (\>= 8.1)
  * Composer
  * Node.js & npm (for Tailwind CSS compilation)
  * A database (MySQL, PostgreSQL, or SQLite)

### Installation Steps

1.  **Clone the Repository**

    ```bash
    git clone [YOUR_REPOSITORY_URL]
    cd facility-booking-system
    ```

2.  **Install PHP Dependencies**

    ```bash
    composer install
    ```

3.  **Configure Environment**

      * Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
      * Generate a unique application key:
        ```bash
        php artisan key:generate
        ```
      * Edit the **.env** file and update your database credentials (`DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

4.  **Run Migrations and Seed Data**
    This step sets up the database schema and populates it with initial data (e.g., a few facility types and an admin user).

    ```bash
    php artisan migrate --seed
    ```

5.  **Install and Compile Frontend Assets**

    ```bash
    npm install
    npm run dev
    # For development/watching changes:
    # npm run watch
    ```

6.  **Start the Local Server**

    ```bash
    php artisan serve
    ```

    > The application will typically be accessible at **[http://127.0.0.1:8000](http://127.0.0.1:8000)**.

-----

## 🧑‍💻 Usage

### Initial Credentials

After seeding the database, you can typically log in with the following users (check your specific seeder for exact details):

| User Type | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@example.com` | `password` |
| **Student** | `student@example.com` | `password` |

### Student Workflow

1.  **Login:** Access the dashboard.
2.  **View Facilities:** Navigate to `/student/facilities`.
3.  **Select Facility:** Click on a facility (e.g., "Nap Pad") to see its details.
4.  **Book Slot:** Click on an **Available** time slot button and confirm the booking in the modal.

### Admin Workflow

1.  **Login:** Access the admin panel (usually `/admin`).
2.  **Manage:** Create new facilities, adjust capacities, or view overall booking statistics.
3.  **Review: Student Feedbacks**

-----

## 🤝 Contributing

This project is currently being developed. If you find any bugs or have feature suggestions, please open an issue in the repository.

-----
