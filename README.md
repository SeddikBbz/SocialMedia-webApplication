# Social Media Web App Using PHP , MongoDB
-----------------------------------------------------------------

## 1/ Documentation
 **Here I'll describe my project Schema for** *Semi-Structred-Data*
 *Schema_SSD.json*
 ```
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "type": "object",
  "properties": {
    "users": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "userId": {"type": "integer"},
          "username": {"type": "string"},
          "fullName": {"type": "string"},
          "email": {"type": "string", "format": "email"},
          "birthdate": {"type": "string", "format": "date"},
          "location": {"type": "string"},
          "bio": {"type": "string"},
          "friends": {"type": "array", "items": {"type": "integer"}},
          "posts": {"type": "array", "items": {"type": "integer"}},
          "createdAt": {"type": "string", "format": "date-time"},
          "role": {"type": "string"}   
        },
        "required": ["userId", "username", "fullName", "email", "birthdate", "location", "bio", "friends", "posts", "createdAt"]
      }
    },
    "posts": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "postId": {"type": "integer"},
          "userId": {"type": "integer"},
          "content": {"type": "string"},
          "likes": {"type": "array", "items": {"type": "integer"}},
          "comments": {
            "type": "array",
            "items": {
              "type": "object",
              "properties": {
                "commentId": {"type": "integer"},
                "userId": {"type": "integer"},
                "postId": {"type": "integer"},
                "content": {"type": "string"},
                "createdAt": {"type": "string", "format": "date-time"}
              },
              "required": ["commentId", "userId", "postId", "content", "createdAt"]
            }
          },
          "createdAt": {"type": "string", "format": "date-time"}
        },
        "required": ["postId", "userId", "content", "likes", "comments", "createdAt"]
      }
    },
    "comments": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "commentId": {"type": "integer"},
          "userId": {"type": "integer"},
          "postId": {"type": "integer"},
          "content": {"type": "string"},
          "createdAt": {"type": "string", "format": "date-time"}
        },
        "required": ["commentId", "userId", "postId", "content", "createdAt"]
      }
    },
    "likes": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "likeId": {"type": "integer"},
          "userId": {"type": "integer"},
          "postId": {"type": "integer"},
          "createdAt": {"type": "string", "format": "date-time"}
        },
        "required": ["likeId", "userId", "postId", "createdAt"]
      }
    }
  },
  "required": ["users", "posts", "comments", "likes"]
}


{*

<!ELEMENT json (users, posts, comments, likes)>

<!ELEMENT users (user+)>
<!ELEMENT user (userId, username, fullName, email, birthdate, location, bio, friends, posts, createdAt)>
<!ELEMENT userId (#PCDATA)>
<!ELEMENT username (#PCDATA)>
<!ELEMENT fullName (#PCDATA)>
<!ELEMENT email (#PCDATA)>
<!ELEMENT birthdate (#PCDATA)>
<!ELEMENT location (#PCDATA)>
<!ELEMENT bio (#PCDATA)>
<!ELEMENT friends (friend+)>
<!ELEMENT friend (#PCDATA)>


<!ELEMENT posts (post+)>
<!ELEMENT post (postId, userId, content, likes, comments, createdAt)>
<!ELEMENT postId (#PCDATA)>
<!ELEMENT content (#PCDATA)>


<!ELEMENT likes (like+)>
<!ELEMENT like (#PCDATA)>
<!ELEMENT comments (comment+)>
<!ELEMENT comment (commentId, userId, postId, content, createdAt)>
<!ELEMENT commentId (#PCDATA)>
<!ELEMENT createdAt (#PCDATA)>
*}
 ```
----------------------------------------------------------------

## 2/ Implementation:

## Overview

This project builds a social media web application using PHP, MongoDB, and a frontend framework (Bootstrap or Tailwind CSS) for a user-friendly experience. It offers functionalities for both users and admins, allowing user registration, role selection, profile management, post creation, commenting, liking, and admin-specific user management.

## Features:

* User Registration and Login
* Role Selection (Admin/User)
* User Profile Management
* Post Creation and Viewing
* Commenting on Posts
* Liking Posts
* Admin User Management (Add, Delete, Edit)

## Technologies:

* Backend: PHP (8.0.30, 8.1.25)
* Database: MongoDB
* Server: XAMPP (Apache + MySQL + PHP + phpMyAdmin)
* Composer (for dependency management)
* Frontend Framework: (Bootstrap & Tailwind CSS)
* HTML, CSS, JavaScript

## Requirements:

   - Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/).
   - Ensure PHP *(8.0,..)* is running. Verify the version using `php -v` in your terminal.
   - Download and install MongoDB from [https://www.mongodb.com/try/download/community](https://www.mongodb.com/try/download/community).
     Follow the installation instructions for your operating system.
   - Install Composer by following the instructions at [https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md](https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md).
   - Download the MongoDB PHP Driver (DLL).

## Installation Instructions:

1. **Important Security Note:**

- Downloading DLLs from unofficial sources can be risky. While PECL (https://pecl.php.net/package/mongodb) was previously a reliable source for PHP extensions, it's no longer officially supported.

- To ensure security and compatibility, we strongly recommend downloading the MongoDB PHP driver directly from the official MongoDB repository on GitHub:

```Bash
  git clone https://github.com/mongodb/mongo-php-driver.git
```
- Navigate to the cloned directory:

```Bash
cd mongo-php-driver
```
-----------------------------------------------------------------------------


 **1.1. Build the DLL (for Windows Users):**

If you're using Windows, follow the instructions in the MongoDB PHP Driver documentation: [https://www.php.net/manual/en/mongodb.installation.windows.php] to build the DLL for your specific PHP version and architecture (x86 or x64).


**1.2. Locate the Extension Directory:**

- Open the XAMPP control panel.
- Click on the "Config" button next to the Apache module.
- Go to the "PHP" tab.
- Look for the line that says "extension_dir". This will indicate the directory where PHP extensions (DLLs) reside. The default is typically C:\xampp\php\ext.

**1.3. Extract the DLL:**

Extract the php_mongodb.dll file (or its equivalent for your operating system) from the downloaded package (or built DLL) into the extension directory you identified in step *1.2*.

**1.4. Enable the Extension in php.ini:**

- Open the php.ini file located in your XAMPP installation directory (e.g., C:\xampp\php). You can use a text editor like Notepad++ to edit it.

- Locate the section for dynamic extensions ([extension] or [Dynamic Extensions]).

- Add the following line to the end of this section, replacing path/to/php_mongodb.dll with the actual path to your extracted DLL:

```Ini, TOML
extension=path/to/php_mongodb.dll
```
- Save the php.ini file.

**1.5. Install dependencies using Composer:**

   ```bash
   composer require mongodb/mongodb
   ```

**1.6. Restart Apache:**

In the XAMPP control panel, click on the "Stop" button next to the Apache module.
Then, click on the "Start" button to restart Apache and activate the changes made to php.ini.

**1.7. Test the Connection:**

Create a simple PHP script to test the connection to your MongoDB database. Here's an example:

```PHP
<?php

$client = new MongoClient("mongodb://localhost:27017"); // Replace with your MongoDB connection details if needed
$db = $client->socialMedia; // Replace with your database name

echo "Connection to MongoDB successful!";

$client->close();

?>
```

- Save this script as test_mongo_connection.php in your XAMPP htdocs folder.

- Access the script in your web browser (e.g., http://localhost/test_mongo_connection.php).

*If you see* **"Connection to MongoDB successful!"**, *the extension is enabled and working correctly.*

---------------------------------------------------------------

2. **Clone the Repository:**

   Open your terminal or command prompt and navigate to your desired project directory. Then, clone this repository using Git:

   ```bash
   git clone https://github.com/SeddikBbz/SocialMedia-webApplication 
   
   ```

3. **Run the Application:**

   - Start your XAMPP Apache server.
   - In your web browser, navigate to `http://localhost/social_web_App` (replace with your project's directory within XAMPP's `htdocs` folder).

**Remember to replace:**
* `socialMedia` name of database with your preferred database name if you need.
* Create collections in MongoDB database (`users,posts,comments,likes,user_admin`).
     

## Usage:

1. Sign up or log in using the provided user registration and login forms.
2. Select your role (Admin/User) during registration or login.
3. Users can manage their profiles, create posts, comment on posts, and like posts.
4. Admins can view, add, edit, and delete users.

**Additional Notes:**

* Consider using security best practices when storing and handling user data (e.g., password hashing).
* For more complex user management, consider using a dedicated authentication service.



## Getting Started with Development:

1. Fork this repository on GitHub.
2. Clone the forked repository to your local machine.
3. Install dependencies using Composer:

   ```bash
   composer install
   ```

4. Make changes to the PHP code, HTML templates, CSS styles, and JavaScript logic as needed.
5. Test your changes thoroughly.

**Contributing:**

* We welcome contributions to this project! Please create pull requests on GitHub to share your improvements.

## Acknowledgements
Special thanks to [MongoDB](https://www.mongodb.com/), [XAMPP](https://www.apachefriends.org/), [Composer](https://getcomposer.org/), [Bootstrap](https://getbootstrap.com/), and [Tailwind CSS](https://tailwindcss.com/) for their fantastic tools and frameworks.

----

Feel free to customize this template further to include any additional information or details specific to your project.

---

## 3/ Project_Pictures : 

<img width="523" alt="Screenshot 2024-03-02 004949" src="https://github.com/SeddikBbz/SocialMedia-webApplication/assets/125443878/37b0b49f-acad-4c87-bc54-5196c9391e56">
<img width="890" alt="Screenshot 2024-03-02 005018" src="https://github.com/SeddikBbz/SocialMedia-webApplication/assets/125443878/96329ca7-a1fd-47f9-8c26-d6f614252a6c">
<img width="846" alt="Screenshot 2024-03-02 005048" src="https://github.com/SeddikBbz/SocialMedia-webApplication/assets/125443878/62f5275d-923a-4a84-a138-d508ab5c0e60">
