-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2021 at 07:18 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eacademy_latest`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_versions`
--

CREATE TABLE `app_versions` (
  `id` int(11) NOT NULL,
  `latest_version` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `app_versions`
--

INSERT INTO `app_versions` (`id`, `latest_version`) VALUES
(1, 8);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `added_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `batch_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `batch_type` int(11) NOT NULL COMMENT '1= batch free , 2=batch paid',
  `batch_price` varchar(100) NOT NULL,
  `batch_offer_price` varchar(50) NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `batch_image` varchar(200) NOT NULL,
  `no_of_student` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`id`, `admin_id`, `batch_name`, `start_date`, `end_date`, `start_time`, `end_time`, `batch_type`, `batch_price`, `batch_offer_price`, `description`, `batch_image`, `no_of_student`, `status`) VALUES
(3, 1, 'Olympiad', '2020-12-25', '2021-03-31', '16:00:00', '17:00:00', 2, '500', '', 'Aenean at quam feugiat, commodo felis dignissim, mollis ex. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; In tempor tempor dui, quis volutpat felis vehicula vitae. Maecenas nec maximus sapien. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam dignissim sem eget sagittis viverra.', '1200px-IMO_logo_svg_201225154326.png', 8, 1),
(4, 1, 'Chemistry', '2020-12-25', '2021-04-28', '16:00:00', '17:00:00', 2, '100', '50', 'Aenean at quam feugiat, commodo felis dignissim, mollis ex. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; In tempor tempor dui, quis volutpat felis vehicula vitae. Maecenas nec maximus sapien. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam dignissim sem eget sagittis viverra.', 'logo-types_201225155016.png', 3, 1),
(16, 1, 'xyz', '2020-10-03', '2021-02-25', '15:00:00', '16:00:00', 1, '', '', '', '', 0, 1),
(10, 1, 'Physics', '2021-02-15', '2021-03-31', '17:00:00', '18:00:00', 2, '20', '15', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed fermentum consequat velit.\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Sed fermentum consequat velit.', 'logo4_210216173407.png', -1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `batch_fecherd`
--

CREATE TABLE `batch_fecherd` (
  `id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `batch_specification_heading` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `batch_fecherd` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `batch_fecherd`
--

INSERT INTO `batch_fecherd` (`id`, `batch_id`, `batch_specification_heading`, `batch_fecherd`) VALUES
(4, 3, 'What will I learn?', '[\"How to solve complex problem quickly?\",\"How to solve numbers error?\",\"How to solve problem orally?\"]'),
(5, 3, 'what will I get?', '[\"100+ Hours tutorial video\",\"75+ Test Papers\",\"1000+ Imp Questions\"]'),
(6, 4, 'What will I get?', '[\"100+ hours Video\",\"24 Hour support\",\"75+ Test series\",\"1000+ Questions\"]'),
(7, 4, 'What will I learn?', '[\"Periodic table \",\"IUPAC name\",\"Organic\",\"Inorganic\"]'),
(8, 5, 'What will I Learn?', '[\"How to solve complex problem quickly?\",\"Lorem Ipsum is simply dummy text of the printing and typesetting industry.\",\"Lorem Ipsum is simply dummy text of the printing and typesetting industry.\",\"Lorem Ipsum is simply dummy text of the printing and typesetting industry.\"]'),
(9, 5, 'What will I get?', '[\"Lorem Ipsum is simply dummy text of the printing and typesetting industry.\",\"Lorem Ipsum is simply dummy text of the printing and typesetting industry.\",\"Lorem Ipsum is simply dummy text of the printing and typesetting industry.\",\"Lorem Ipsum is simply dummy text of the printing and typesetting industry.\"]'),
(10, 9, 'What will I learn?', '[\"Lorem ipsum dolor sit amet, consectetur adipiscing elit\",\"Lorem ipsum dolor sit amet, consectetur adipiscing elit\",\"Lorem ipsum dolor sit amet, consectetur adipiscing elit\"]'),
(11, 10, 'What will I learn?', '[\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed fermentum consequat velit.\",\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed fermentum consequat velit.\",\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed fermentum consequat velit.\",\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed fermentum consequat velit.\"]'),
(12, 10, 'What will I get ?', '[\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed fermentum consequat velit.\",\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed fermentum consequat velit.\"]'),
(13, 11, 'aaaaaaaa', '[\"aaaaa\",\"aaaaa\"]'),
(14, 12, 'aaaaaaaaaa', '[\"aaaaaaa\"]');

-- --------------------------------------------------------

--
-- Table structure for table `batch_subjects`
--

CREATE TABLE `batch_subjects` (
  `id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `chapter` varchar(500) NOT NULL,
  `sub_start_date` date NOT NULL,
  `sub_end_date` date NOT NULL,
  `sub_start_time` time NOT NULL,
  `sub_end_time` time NOT NULL,
  `chapter_status` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'id of completed chapter',
  `chapter_complt_date` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `total_chapter_complt_date` datetime NOT NULL,
  `added_on` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `batch_subjects`
--

INSERT INTO `batch_subjects` (`id`, `batch_id`, `teacher_id`, `subject_id`, `chapter`, `sub_start_date`, `sub_end_date`, `sub_start_time`, `sub_end_time`, `chapter_status`, `chapter_complt_date`, `total_chapter_complt_date`, `added_on`) VALUES
(39, 3, 5, 2, '[\"2\",\"3\",\"4\",\"5\"]', '2020-12-25', '2021-01-31', '16:00:00', '17:00:00', '', '', '0000-00-00 00:00:00', '2021-02-02 14:45:00'),
(47, 4, 4, 3, '[\"6\",\"7\",\"8\",\"9\"]', '2020-12-25', '2021-01-31', '16:00:00', '17:00:00', '', '', '0000-00-00 00:00:00', '2021-02-16 17:33:53'),
(50, 16, 4, 3, '[\"8\"]', '2020-12-18', '2021-01-16', '12:00:00', '16:00:00', '', '', '0000-00-00 00:00:00', '2021-02-17 15:52:55'),
(48, 10, 5, 2, '[\"2\",\"3\",\"4\",\"5\"]', '2021-02-15', '2021-03-31', '17:00:00', '18:00:00', '', '', '0000-00-00 00:00:00', '2021-02-16 17:34:07');

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` int(11) NOT NULL,
  `title` text CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `image` text CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `admin_id` int(11) NOT NULL,
  `added_by` varchar(100) NOT NULL,
  `status` int(11) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id`, `title`, `image`, `description`, `admin_id`, `added_by`, `status`, `create_at`) VALUES
(3, 'Aenean at quam feugiat, commodo felis dignissim, mollis ex.', 'courses_bg_210109103933.png', '<p class=\"edu_sglblog_des mb_30\" xss=removed>Aenean <span>at<span> quam feugiat, commodo felis dignissim, mollis ex. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae Aenean at quam feugiat, commodo felis dignissim, mollis ex. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae Aenean at quam feugiat, commodo felis dignissim, mollis ex. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae Aenean at quam feugiat, commodo felis dignissim, mollis ex. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curaeAenean at quam feugiat, commodo felis dignissim, mollis ex. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae</p><p class=\"edu_sglblog_des\" xss=removed>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.Lorem Ipsum is simply dummy text of the printing and typesetting printer a galley Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard text ever since the.</p>', 1, '1', 1, '2021-01-09 10:39:33'),
(4, 'aasasadad dsdsad', 'edit_blog_210211112019.png', '&lt;iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/swPgI7iQGeU\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen=\"\"&gt;&lt;/iframe&gt;', 1, '1', 1, '2021-01-09 13:04:12'),
(5, 'this is new blog', '1_srSO6S7Q0N-Y9iOwdVah0A_210216143433.jpeg', '<strong xss=removed>Lorem Ipsum</strong><span xss=removed> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</span>', 1, '', 1, '2021-02-16 14:34:33');

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE `blog_comments` (
  `id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `admin_id` int(100) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `user_role` varchar(11) NOT NULL,
  `user_name` text CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `user_email` text CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `user_mobile` text CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `user_image` varchar(100) NOT NULL,
  `comments` text CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 = painding ,1 =complete , 2 = decline',
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `blog_comments`
--

INSERT INTO `blog_comments` (`id`, `blog_id`, `admin_id`, `user_id`, `user_role`, `user_name`, `user_email`, `user_mobile`, `user_image`, `comments`, `status`, `create_at`) VALUES
(9, 3, 1, '1', '1', 'admin', 'admin@eacademy.com', '', 'student_img.png', 'admin test', 1, '2021-01-19 16:34:45'),
(10, 3, 1, '', '', 'Voilence', 'sachin.mandloi@pixelnx.com', '7894561325', 'student_img.png', 'bina login', 1, '2021-01-19 16:35:50'),
(11, 5, 1, '1', '1', 'admin', 'admin@eacademy.com', '', 'student_img.png', 'hi', 1, '2021-02-16 14:34:54');

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments_reply`
--

CREATE TABLE `blog_comments_reply` (
  `id` int(11) NOT NULL,
  `comment_id` varchar(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `name` text CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `email` text CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `reply` text CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `image` varchar(100) NOT NULL,
  `status` int(11) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `blog_comments_reply`
--

INSERT INTO `blog_comments_reply` (`id`, `comment_id`, `user_id`, `name`, `email`, `mobile`, `reply`, `image`, `status`, `create_at`) VALUES
(1, '9', '1', 'admin', 'admin@eacademy.com', '', 'admin replay', 'student_img.png', 1, '2021-01-19 16:35:10'),
(2, '10', '5', 'Alexa', 'alexa@eacademy.com', '', 'teacher comment', 'student_img.png', 1, '2021-01-19 16:37:00');

-- --------------------------------------------------------

--
-- Table structure for table `certificate`
--

CREATE TABLE `certificate` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `added_id` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `certificate`
--

INSERT INTO `certificate` (`id`, `student_id`, `batch_id`, `added_id`, `date`) VALUES
(1, 18, 4, 1, '2020-12-29');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_setting`
--

CREATE TABLE `certificate_setting` (
  `id` int(11) NOT NULL,
  `heading` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sub_heading` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `certificate_logo` varchar(500) NOT NULL,
  `signature_image` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `certificate_setting`
--

INSERT INTO `certificate_setting` (`id`, `heading`, `sub_heading`, `title`, `description`, `certificate_logo`, `signature_image`) VALUES
(1, 'Awesome', 'You are awesome', 'Certificate', 'I am awesome too', 'elogo-01.png', 'sign-1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `chapter_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `no_of_questions` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `subject_id`, `chapter_name`, `status`, `no_of_questions`) VALUES
(2, 2, 'Addition', 1, 6),
(3, 2, 'Subtractions', 1, 4),
(4, 2, 'Division', 1, 14),
(5, 2, 'Multiplication', 1, 11),
(6, 3, 'Periodic table', 1, 0),
(7, 3, 'IUPAC name', 1, 1),
(8, 3, 'Organic', 1, 2),
(9, 3, 'Inorganic', 1, 2),
(21, 5, 'Acceleration', 1, 0),
(20, 5, 'Modern Physics', 1, 4),
(19, 5, 'Units', 1, 0),
(18, 5, 'Laws of motions', 1, 0),
(22, 5, 'Displacement', 1, 0),
(23, 5, 'Speed', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `admin_id` int(11) NOT NULL,
  `course_duration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `class_size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time_duration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enquiry`
--

CREATE TABLE `enquiry` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL COMMENT '1 - mock, 2 - practice',
  `format` int(11) NOT NULL COMMENT '1 - Shuffle, 2 - Fix',
  `batch_id` int(11) NOT NULL,
  `total_question` varchar(255) NOT NULL,
  `time_duration` varchar(255) NOT NULL COMMENT 'In Minute Only',
  `question_ids` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mock_sheduled_date` date NOT NULL,
  `mock_sheduled_time` time NOT NULL,
  `status` int(11) NOT NULL,
  `added_by` int(11) NOT NULL,
  `added_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `admin_id`, `name`, `type`, `format`, `batch_id`, `total_question`, `time_duration`, `question_ids`, `mock_sheduled_date`, `mock_sheduled_time`, `status`, `added_by`, `added_at`) VALUES
(21, 1, 'pr1', 2, 1, 3, '7', '5', '[\"46\",\"45\",\"44\",\"43\",\"42\",\"39\",\"37\"]', '0000-00-00', '00:00:00', 1, 1, '2021-02-17 15:39:08'),
(22, 1, 'pr2', 2, 1, 10, '5', '5', '[\"46\",\"45\",\"44\",\"43\",\"42\"]', '0000-00-00', '00:00:00', 1, 1, '2021-02-17 15:39:41'),
(23, 1, 'moc1', 1, 1, 3, '4', '5', '[\"46\",\"45\",\"44\",\"43\"]', '2021-02-17', '15:41:00', 1, 1, '2021-02-17 15:40:38'),
(24, 1, 'moc2', 1, 1, 3, '4', '5', '[\"46\",\"45\",\"44\",\"43\"]', '2021-02-17', '15:47:00', 1, 1, '2021-02-17 15:46:06'),
(25, 1, 'pr2', 2, 1, 3, '19', '10', '[\"46\",\"45\",\"44\",\"43\",\"39\",\"37\",\"36\",\"35\",\"32\",\"31\",\"9\",\"8\",\"7\",\"6\",\"5\",\"4\",\"3\",\"2\",\"1\"]', '0000-00-00', '00:00:00', 1, 1, '2021-02-17 16:02:11'),
(26, 1, 'moc3', 1, 1, 3, '5', '5', '[\"46\",\"45\",\"44\",\"43\",\"39\"]', '2021-02-17', '16:10:00', 1, 1, '2021-02-17 16:03:12'),
(27, 1, 'moc4', 1, 1, 3, '5', '5', '[\"39\",\"43\",\"44\",\"45\",\"46\"]', '2021-02-17', '16:25:00', 1, 1, '2021-02-17 16:21:59');

-- --------------------------------------------------------

--
-- Table structure for table `extra_classes`
--

CREATE TABLE `extra_classes` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) NOT NULL,
  `batch_id` varchar(500) NOT NULL,
  `added_at` datetime NOT NULL,
  `completed_date_time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `extra_classes`
--

INSERT INTO `extra_classes` (`id`, `admin_id`, `date`, `start_time`, `end_time`, `teacher_id`, `description`, `status`, `batch_id`, `added_at`, `completed_date_time`) VALUES
(1, 1, '2021-02-27', '16:00:00', '20:00:00', 4, 'saloni saloni salonisaloni saloni shrivastava saloni shrivastava saloni saloni saloni saloni', 'Incomplete', '[\"4\"]', '2021-02-01 17:37:30', '0000-00-00 00:00:00'),
(2, 1, '2021-02-27', '12:00:00', '20:00:00', 5, 'If you get this error and the terminal is not descriptive enough to help you, Heroku might be able to tell you what is wrong through their website. Try the following:\r\n\r\nlog in to Heroku and go to your dashboard;\r\nselect the problem application from the list; and,\r\ncheck the build logs under the \"Activity\" tab.', 'Incomplete', '[\"4\"]', '2021-02-01 17:49:32', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `extra_class_attendance`
--

CREATE TABLE `extra_class_attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `added_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(255) NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `title`, `icon`, `description`, `status`) VALUES
(1, 'Trending Courses', 'icofont-star-shape', 'consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ipsum suspendisse.', 1),
(2, 'Online E-Books', 'icofont-read-book-alt', 'consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ipsum suspendisse.', 1),
(3, 'Expert Guidance', 'icofont-user-alt-5', 'consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ipsum suspendisse.', 1),
(4, 'Certified Teachers', 'icofont-teacher', 'consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ipsum suspendisse.', 1),
(5, 'Adaptive Learning', 'icofont-support', 'consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ipsum suspendisse.', 1),
(6, 'Trending Courses', 'icofont-paper', 'consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ipsum suspendisse.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `frontend_details`
--

CREATE TABLE `frontend_details` (
  `id` int(11) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `youtube` varchar(255) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `instagram` varchar(255) NOT NULL,
  `linkedin` varchar(255) NOT NULL,
  `map_api` varchar(255) NOT NULL,
  `slider_details` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cont_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cont_sub_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cont_form_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `faci_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `faci_sub_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `no_of_facility` int(11) NOT NULL,
  `frst_crse_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `frst_crse_sub_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `frst_crse_desc` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sec_crse_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sec_crse_sub_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `no_of_course` int(11) NOT NULL,
  `abt_frst_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abt_frst_sub_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abt_year` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abt_frst_desc` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abt_frst_img` varchar(255) NOT NULL,
  `abt_sec_img` varchar(255) NOT NULL,
  `abt_sec_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abt_sec_desc` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abt_secbtn_text` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abt_secbtn_url` varchar(255) NOT NULL,
  `abt_thrd_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abt_thrd_sub_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abt_thrd_desc` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abt_thrd_img` varchar(255) NOT NULL,
  `total_toppers` int(11) NOT NULL,
  `trusted_students` int(11) NOT NULL,
  `years_of_histry` int(11) NOT NULL,
  `testimonial` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `testi_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `testi_subheading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `selectn_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `selectn_subheading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `selection` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teacher_heading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teacher_subheading` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `no_of_teacher` int(11) NOT NULL,
  `header_btn_txt` varchar(255) NOT NULL,
  `header_btn_url` varchar(255) NOT NULL,
  `client_imgs` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `frontend_details`
--

INSERT INTO `frontend_details` (`id`, `mobile`, `email`, `address`, `facebook`, `youtube`, `twitter`, `instagram`, `linkedin`, `map_api`, `slider_details`, `cont_heading`, `cont_sub_heading`, `cont_form_heading`, `faci_heading`, `faci_sub_heading`, `no_of_facility`, `frst_crse_heading`, `frst_crse_sub_heading`, `frst_crse_desc`, `sec_crse_heading`, `sec_crse_sub_heading`, `no_of_course`, `abt_frst_heading`, `abt_frst_sub_heading`, `abt_year`, `abt_frst_desc`, `abt_frst_img`, `abt_sec_img`, `abt_sec_heading`, `abt_sec_desc`, `abt_secbtn_text`, `abt_secbtn_url`, `abt_thrd_heading`, `abt_thrd_sub_heading`, `abt_thrd_desc`, `abt_thrd_img`, `total_toppers`, `trusted_students`, `years_of_histry`, `testimonial`, `testi_heading`, `testi_subheading`, `selectn_heading`, `selectn_subheading`, `selection`, `teacher_heading`, `teacher_subheading`, `no_of_teacher`, `header_btn_txt`, `header_btn_url`, `client_imgs`) VALUES
(1, '1234567890', 'info@themes91.in', '04 A Agroha Nagar, Dewas, Madhya Pradesh', 'https://www.facebook.com', 'https://www.youtube.com', 'https://www.twitter.com', 'https://www.instagram.com', 'https://www.linkedin.com', '', '{\"slider_heading\":[\"Choose Best For Your Education\",\"Choose Best For Your Education\",\"Choose Best For Your Education\"],\"slider_subheading\":[\"Welcome to E-Academy\",\"Welcome to E-Academy\",\"Welcome to E-Academy\"],\"slider_desc\":[\"\",\"\",\"\"],\"slider_img\":[\"slider3.png\",\"slider1.png\",\"slider2.png\"]}', 'Contact Us For You Query', 'START WITH US', 'Send Us A Message', 'Why Choose Our Academy', 'Our Facilities', 6, 'Online Learning Plateform', 'WE ARE E- ACADEMY', 'int occaecat cupidatat noesn proident sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut peerspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantiuws totam rem aperiame.<br /><br />int occaecat cupidatat noesn proident sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut peerspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantiuws totam rem aperiame.', 'Choose Your Course', 'WE ENHANCE YOUR TALENT', 6, 'Why Choose Us', 'ABOUT E-ACADEMY', '1997', 'Consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore eesdoeit dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation and in ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum<br /><br />Excepteur sint occaecat cupidatat noesn proident sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut peerspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantiuws totam rem aperiam, eaque ipsa quae.Consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore eesdoeit dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.', 'about_img1.png', 'about_img2.png', 'We Take Care Of Your Careers Do Not Worry', 'We Are Very Cost Friendly And We Would Love To Be A Part Of Your Home Happiness For A Long Lorem Ipsum Dolor Sit Amet, Consectetur Adipisicing Elit Sed Eiusmod.', 'Contact Us', 'http://kamleshyadav.in/e-academy/contact-us', 'Why Choose Us From Thousands', 'OUR MISSION', 'Consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore eesdoeit dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation and in ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum.<br /><br />Excepteur sint occaecat cupidatat noesn proident sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut peerspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantiuws totam rem aperiam, eaque ipsa quae.', 'vission_img.png', 654, 200, 50, '{\"4\":\"fsghhjjgh\",\"3\":\"fgfhg jjyjy qweqe qewfre ret\",\"1\":\"Consectetur adipiscing elit, sed do eiusmod tempor incididunt uerset labore et dolore magna aliqua. Qesuis ipsum esuspendisse ultriceies gravida Risus commodo viverra andes aecenas accumsan lacus vel facilisis. \",\"2\":\"Consectetur adipiscing elit, sed do eiusmod tempor incididunt uerset labore et dolore magna aliqua. Qesuis ipsum esuspendisse ultriceies gravida Risus commodo viverra andes aecenas accumsan lacus vel facilisis. \"}', 'What Student Says', 'E- ACADEMY REVIEWS', 'Our Selections', 'TOPPERS ARE HERE', '{\"2\":\"student\",\"3\":\"student\",\"1\":\"student\"}', 'Qualified Teacher', 'OUR EXPERTS', 6, '', '', '[\"01.png\",\"02.png\",\"03.png\",\"04.png\",\"05.png\",\"06.png\"]');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `video_url` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `type`, `image`, `video_url`, `status`) VALUES
(1, 'dsads', 'Image', 'login_bg.jpg', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `general_settings`
--

CREATE TABLE `general_settings` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `key_text` text NOT NULL,
  `velue_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `general_settings`
--

INSERT INTO `general_settings` (`id`, `title`, `key_text`, `velue_text`) VALUES
(1, 'payment gateway type select 1 = razorpay, 2 paypal', 'payment_type', '1'),
(2, 'razorpay key id ', 'razorpay_key_id', ''),
(3, 'razorpay secret key', 'razorpay_secret_key', ''),
(4, 'paypal client id', 'paypal_client_id', ''),
(5, 'paypal secret key', 'paypal_secret_key', ''),
(6, 'select language type ', 'language_name', 'english'),
(7, 'select currency code', 'currency_code', 'INR'),
(8, 'select currency decimal code', 'currency_decimal_code', '₹'),
(9, 'currency converter api', 'currency_converter_api', ''),
(10, 'send mails SMTP ', 'smtp_mail', ''),
(11, 'smtp password mail', 'smtp_pwd', ''),
(12, 'smtp server type mail', 'server_type', 'smtp'),
(13, 'smtp host mail', 'smtp_host', ''),
(14, 'smtp host mails', 'smtp_port', ''),
(15, 'smtp smtp encryption', 'smtp_encryption', 'ssl'),
(16, 'sandbox accounts', 'sandbox_accounts', '');

-- --------------------------------------------------------

--
-- Table structure for table `homeworks`
--

CREATE TABLE `homeworks` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `subject_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `added_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `homeworks`
--

INSERT INTO `homeworks` (`id`, `admin_id`, `teacher_id`, `date`, `subject_id`, `batch_id`, `description`, `added_at`) VALUES
(1, 1, 4, '2020-12-26', 3, 5, 'gfhgf jgfgh fhgf jgh', '2020-12-26 14:43:23'),
(2, 1, 5, '2020-12-30', 2, 5, 'homework', '2020-12-30 12:57:30'),
(3, 1, 5, '2021-02-02', 2, 3, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '2021-02-02 14:57:32'),
(4, 1, 5, '2021-02-04', 2, 3, 'jhgfgjhf gffjhgf gjhfghf gjhfgjhd gfjhdhjkg', '2021-02-03 12:02:26'),
(5, 1, 5, '2021-02-13', 2, 9, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean placerat condimentum tellus. Maecenas facilisis orci eget quam ornare, sit amet fringilla purus facilisis. Sed malesuada mi nibh, vitae pretium nisi dignissim vel. Maecenas eu magna a orci dictum imperdiet eu ut tortor. Phasellus orci dui, vehicula et pretium id, convallis nec mauris. In et justo vitae velit convallis dictum. Nunc non volutpat velit. Vivamus aliquam ultricies maximus. Integer lacinia in ex sed bibendum. Morbi blandit vulputate posuere. Nullam augue lorem, feugiat at augue id, vehicula molestie nisi.', '2021-02-13 16:49:35'),
(6, 1, 5, '2021-02-13', 2, 3, 'sadsadsad', '2021-02-13 18:10:45');

-- --------------------------------------------------------

--
-- Table structure for table `leave_management`
--

CREATE TABLE `leave_management` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `leave_msg` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `total_days` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `live_class_history`
--

CREATE TABLE `live_class_history` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `start_time` varchar(500) NOT NULL,
  `end_time` varchar(500) NOT NULL,
  `date` date NOT NULL,
  `entry_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `live_class_setting`
--

CREATE TABLE `live_class_setting` (
  `id` int(11) NOT NULL,
  `batch` int(11) NOT NULL,
  `zoom_api_key` varchar(500) NOT NULL,
  `zoom_api_secret` varchar(500) NOT NULL,
  `meeting_number` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `added_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mock_result`
--

CREATE TABLE `mock_result` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `paper_id` int(11) NOT NULL,
  `paper_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `submit_time` time NOT NULL,
  `total_question` int(11) NOT NULL,
  `time_duration` varchar(255) NOT NULL,
  `attempted_question` int(11) NOT NULL,
  `question_answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `percentage` double NOT NULL,
  `added_on` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mock_result`
--

INSERT INTO `mock_result` (`id`, `admin_id`, `student_id`, `paper_id`, `paper_name`, `date`, `start_time`, `submit_time`, `total_question`, `time_duration`, `attempted_question`, `question_answer`, `percentage`, `added_on`) VALUES
(1, 1, 1, 2, 'mock1', '2020-12-26', '14:05:00', '14:05:00', 9, '5', 9, '{\"8\":\"A\",\"1\":\"B\",\"6\":\"B\",\"4\":\"B\",\"5\":\"B\",\"7\":\"B\",\"9\":\"B\",\"3\":\"A\",\"2\":\"B\"}', 30.56, '2020-12-26 14:05:45'),
(2, 1, 45, 13, 'ghffg', '2021-02-03', '14:27:00', '14:27:00', 10, '15', 0, '{}', 0, '2021-02-03 14:27:36'),
(3, 1, 45, 14, 'dads', '2021-02-03', '14:27:00', '14:29:00', 8, '15', 8, '{\"8\":\"B\",\"39\":\"B\",\"40\":\"A\",\"6\":\"B\",\"7\":\"B\",\"5\":\"B\",\"4\":\"B\",\"3\":\"C\"}', 37.5, '2021-02-03 14:29:02'),
(4, 1, 24, 23, 'moc1', '2021-02-17', '15:41:00', '15:42:00', 4, '5', 4, '{\"43\":\"C\",\"44\":\"A\",\"45\":\"D\",\"46\":\"A\"}', 100, '2021-02-17 15:42:45'),
(5, 1, 25, 24, 'moc2', '2021-02-17', '15:48:00', '15:51:00', 4, '5', 3, '{\"44\":\"A\",\"45\":\"D\",\"46\":\"A\"}', 75, '2021-02-17 15:51:04'),
(6, 1, 24, 24, 'moc2', '2021-02-17', '15:47:00', '15:52:00', 4, '5', 4, '{\"43\":\"C\",\"44\":\"A\",\"45\":\"D\",\"46\":\"A\"}', 100, '2021-02-17 15:52:06'),
(7, 1, 1, 26, 'moc3', '2021-02-17', '16:10:00', '16:12:00', 5, '5', 5, '{\"39\":\"C\",\"43\":\"A\",\"44\":\"A\",\"45\":\"B\",\"46\":\"D\"}', 0, '2021-02-17 16:12:34'),
(8, 1, 24, 26, 'moc3', '2021-02-17', '16:10:00', '16:12:00', 5, '5', 5, '{\"39\":\"B\",\"43\":\"C\",\"44\":\"A\",\"45\":\"D\",\"46\":\"A\"}', 100, '2021-02-17 16:12:50'),
(9, 1, 1, 27, 'moc4', '2021-02-17', '16:25:00', '16:25:00', 5, '5', 5, '{\"39\":\"B\",\"43\":\"B\",\"44\":\"B\",\"45\":\"D\",\"46\":\"A\"}', 50, '2021-02-17 16:25:16'),
(10, 1, 25, 27, 'moc4', '2021-02-17', '16:25:00', '16:25:00', 5, '5', 4, '{\"39\":\"B\",\"43\":\"C\",\"44\":\"A\",\"45\":\"D\"}', 80, '2021-02-17 16:25:35'),
(11, 1, 24, 27, 'moc4', '2021-02-17', '16:25:00', '16:26:00', 5, '5', 5, '{\"39\":\"B\",\"43\":\"C\",\"44\":\"A\",\"45\":\"D\",\"46\":\"A\"}', 100, '2021-02-17 16:26:02');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `notice_for` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `date` date NOT NULL,
  `admin_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `added_by` varchar(255) NOT NULL,
  `read_status` int(11) NOT NULL,
  `added_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `title`, `description`, `notice_for`, `status`, `date`, `admin_id`, `student_id`, `teacher_id`, `added_by`, `read_status`, `added_at`) VALUES
(1, 'Test notice', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', 'Both', 1, '2020-12-26', 1, 0, 0, '', 1, '2020-12-26 11:29:34'),
(2, 'asasasasasa asasas saloni shrivastava ', 'saloni shrivastava saloniv shrivastava saloni shrivastava saloni shrivastava saloni shrivastava', '', 1, '2021-02-01', 1, 43, 0, '1', 1, '2021-02-01 17:14:00'),
(3, 'Improper attendance in class', 'Hi why are you not coming in the class? ', '', 1, '2021-02-02', 1, 45, 0, '1', 1, '2021-02-02 15:21:55'),
(4, 'covid-19 pandemic', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean placerat condimentum tellus. Maecenas facilisis orci eget quam ornare, sit amet fringilla purus facilisis. Sed malesuada mi nibh, vitae pretium nisi dignissim vel. Maecenas eu magna a orci dictum imperdiet eu ut tortor. Phasellus orci dui, vehicula et pretium id, convallis nec mauris. In et justo vitae velit convallis dictum. Nunc non volutpat velit. Vivamus aliquam ultricies maximus. Integer lacinia in ex sed bibendum. Morbi blandit vulputate posuere. Nullam augue lorem, feugiat at augue id, vehicula molestie nisi.', 'Student', 1, '2021-02-13', 1, 0, 0, '', 0, '2021-02-13 17:18:37');

-- --------------------------------------------------------

--
-- Table structure for table `practice_result`
--

CREATE TABLE `practice_result` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `paper_id` int(11) NOT NULL,
  `paper_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `submit_time` time NOT NULL,
  `total_question` int(11) NOT NULL,
  `time_duration` varchar(255) NOT NULL,
  `attempted_question` int(11) NOT NULL,
  `question_answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `percentage` double NOT NULL,
  `added_on` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `practice_result`
--

INSERT INTO `practice_result` (`id`, `admin_id`, `student_id`, `paper_id`, `paper_name`, `date`, `start_time`, `submit_time`, `total_question`, `time_duration`, `attempted_question`, `question_answer`, `percentage`, `added_on`) VALUES
(1, 1, 1, 1, 'Rapidpro', '2020-12-26', '12:05:00', '12:09:00', 9, '5', 9, '{\"8\":\"C\",\"1\":\"A\",\"6\":\"A\",\"7\":\"A\",\"4\":\"B\",\"5\":\"B\",\"9\":\"A\",\"3\":\"B\",\"2\":\"A\"}', 16.67, '2020-12-26 12:09:45'),
(2, 1, 1, 1, 'Rapidpro', '2020-12-26', '12:28:00', '12:29:00', 9, '5', 9, '{\"8\":\"A\",\"1\":\"A\",\"6\":\"B\",\"7\":\"B\",\"5\":\"B\",\"4\":\"B\",\"9\":\"B\",\"3\":\"B\",\"2\":\"B\"}', 2.78, '2020-12-26 12:29:12'),
(3, 1, 1, 1, 'Rapidpro', '2020-12-26', '13:07:00', '13:07:00', 9, '5', 9, '{\"1\":\"B\",\"8\":\"A\",\"6\":\"B\",\"5\":\"B\",\"7\":\"D\",\"4\":\"B\",\"9\":\"A\",\"3\":\"A\",\"2\":\"B\"}', 44.44, '2020-12-26 13:07:39'),
(5, 1, 1, 1, 'Rapidpro', '2020-12-30', '13:59:00', '14:01:00', 9, '5', 9, '{\"8\":\"A\",\"1\":\"A\",\"6\":\"A\",\"4\":\"B\",\"7\":\"A\",\"5\":\"B\",\"9\":\"A\",\"3\":\"A\",\"2\":\"B\"}', 30.56, '2020-12-30 14:01:04'),
(6, 1, 1, 6, 'Voilence', '2021-01-27', '17:39:00', '17:39:00', 3, '10', 1, '{\"30\":\"A\"}', -8.33, '2021-01-27 17:39:58'),
(7, 1, 1, 6, 'Voilence', '2021-01-28', '10:49:00', '10:50:00', 3, '10', 3, '{\"29\":\"A\",\"30\":\"B\",\"31\":\"D\"}', -25, '2021-01-28 10:50:01'),
(8, 1, 43, 7, 'Mathematics', '2021-01-29', '15:36:00', '15:37:00', 10, '6', 6, '{\"35\":\"B\",\"36\":\"B\",\"29\":\"B\",\"30\":\"A\",\"31\":\"C\",\"32\":\"A\"}', 22.5, '2021-01-29 15:37:31'),
(9, 1, 43, 8, 'data structure', '2021-02-01', '11:57:00', '11:57:00', 10, '5', 8, '{\"33\":\"B\",\"11\":\"B\",\"34\":\"A\",\"35\":\"A\",\"29\":\"A\",\"9\":\"A\",\"31\":\"A\",\"32\":\"A\"}', 5, '2021-02-01 11:57:44'),
(10, 1, 43, 8, 'data structure', '2021-02-01', '18:20:00', '18:25:00', 10, '5', 0, '{}', 0, '2021-02-01 18:25:16'),
(11, 1, 43, 9, 'data structure', '2021-02-02', '10:19:00', '10:19:00', 1, '2', 1, '{\"38\":\"A\"}', 100, '2021-02-02 10:19:12'),
(12, 1, 43, 10, '1234', '2021-02-02', '10:41:00', '10:42:00', 2, '5', 2, '{\"38\":\"A\",\"40\":\"D\"}', 37.5, '2021-02-02 10:42:27'),
(13, 1, 45, 6, 'Voilence', '2021-02-02', '16:41:00', '16:42:00', 3, '10', 3, '{\"29\":\"A\",\"30\":\"A\",\"31\":\"A\"}', -25, '2021-02-02 16:43:36'),
(14, 1, 45, 6, 'Voilence', '2021-02-03', '11:16:00', '11:20:00', 3, '10', 3, '{\"30\":\"A\",\"31\":\"A\",\"29\":\"A\"}', -25, '2021-02-03 11:20:56'),
(15, 1, 45, 6, 'Voilence', '2021-02-03', '11:21:00', '11:23:00', 3, '10', 2, '{\"30\":\"B\",\"31\":\"D\"}', -16.67, '2021-02-03 11:23:27'),
(16, 1, 45, 6, 'Voilence', '2021-02-03', '11:23:00', '11:24:00', 3, '10', 1, '{\"30\":\"D\"}', 33.33, '2021-02-03 11:24:46'),
(17, 1, 45, 6, 'Voilence', '2021-02-03', '14:02:00', '14:02:00', 3, '10', 1, '{\"31\":\"A\"}', -8.33, '2021-02-03 14:02:23'),
(18, 1, 43, 11, 'dfde', '2021-02-03', '14:11:00', '14:12:00', 1, '2', 1, '{\"40\":\"C\"}', -25, '2021-02-03 14:12:02'),
(19, 1, 45, 12, 'yhf', '2021-02-03', '14:15:00', '14:19:00', 12, '15', 10, '{\"1\":\"B\",\"8\":\"B\",\"39\":\"C\",\"4\":\"B\",\"7\":\"B\",\"5\":\"B\",\"11\":\"B\",\"9\":\"A\",\"3\":\"C\",\"2\":\"D\"}', 41.67, '2021-02-03 14:19:17'),
(20, 1, 43, 16, 'ghjg', '2021-02-03', '14:59:00', '14:59:00', 1, '5', 1, '{\"39\":\"B\"}', 100, '2021-02-03 14:59:35'),
(21, 1, 43, 12, 'yhf', '2021-02-03', '14:59:00', '14:59:00', 12, '15', 5, '{\"11\":\"A\",\"3\":\"B\",\"5\":\"B\",\"7\":\"B\",\"8\":\"A\"}', 0, '2021-02-03 14:59:58'),
(22, 1, 43, 12, 'yhf', '2021-02-03', '15:21:00', '15:22:00', 12, '15', 12, '{\"11\":\"B\",\"1\":\"D\",\"2\":\"D\",\"3\":\"A\",\"4\":\"B\",\"5\":\"D\",\"6\":\"B\",\"39\":\"B\",\"7\":\"D\",\"8\":\"B\",\"9\":\"C\",\"40\":\"B\"}', 58.33, '2021-02-03 15:22:19'),
(23, 1, 43, 11, 'dfde', '2021-02-10', '16:44:00', '16:44:00', 1, '2', 1, '{\"40\":\"A\"}', -25, '2021-02-10 16:44:26'),
(24, 1, 46, 4, 'English', '2021-02-13', '14:31:00', '14:32:00', 9, '5', 8, '{\"1\":\"C\",\"2\":\"C\",\"3\":\"A\",\"4\":\"C\",\"5\":\"B\",\"7\":\"A\",\"8\":\"D\",\"9\":\"D\"}', 19.44, '2021-02-13 14:32:03'),
(25, 1, 24, 21, 'pr1', '2021-02-17', '15:56:00', '15:57:00', 7, '5', 7, '{\"37\":\"C\",\"39\":\"C\",\"42\":\"C\",\"43\":\"C\",\"44\":\"A\",\"45\":\"D\",\"46\":\"A\"}', 64.29, '2021-02-17 15:57:22');

-- --------------------------------------------------------

--
-- Table structure for table `privacy_policy_data`
--

CREATE TABLE `privacy_policy_data` (
  `id` int(11) NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `privacy_policy_data`
--

INSERT INTO `privacy_policy_data` (`id`, `description`) VALUES
(1, '&lt;h2 style=&quot;margin-bottom: 10px; font-family: DauphinPlain; font-size: 24px; line-height: 24px; color: rgb(0, 0, 0); text-transform: none;&quot;&gt;Where does it come from?&lt;/h2&gt;&lt;p style=&quot;margin-bottom: 10px; font-family: DauphinPlain; font-size: 24px; line-height: 24px; color: rgb(0, 0, 0); text-transform: none;&quot;&gt;&lt;span style=&quot;font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; font-size: 14px; text-align: justify;&quot;&gt;Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of &quot;de Finibus Bonorum et Malorum&quot; (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, &quot;Lorem ipsum dolor sit amet..&quot;, comes from a line in section 1.10.32.&lt;/span&gt;&lt;/p&gt;&lt;h2 style=&quot;margin-bottom: 10px; font-family: DauphinPlain; font-size: 24px; line-height: 24px; color: rgb(0, 0, 0); text-transform: none;&quot;&gt;Where can I get some?&lt;/h2&gt;&lt;p style=&quot;margin-bottom: 10px; font-family: DauphinPlain; font-size: 24px; line-height: 24px; color: rgb(0, 0, 0); text-transform: none;&quot;&gt;&lt;span style=&quot;font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; font-size: 14px; text-align: justify;&quot;&gt;There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don&#039;t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn&#039;t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.&lt;/span&gt;&lt;/p&gt;');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `question` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `options` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `answer` varchar(255) NOT NULL,
  `added_by` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `added_on` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `admin_id`, `subject_id`, `chapter_id`, `question`, `options`, `answer`, `added_by`, `status`, `category`, `added_on`) VALUES
(1, 1, 2, 5, 'If you add 1000 to 29898, you obtain', '[39898,30898,29998,29908]', 'B', 1, 1, 0, '2020-12-25 16:25:00'),
(2, 1, 2, 5, 'Which two numbers add up to a sum greater than 1000?', '[\"450 and 545\",\"999 and 1\",\"893 and 100\",\"989 and 12\"]', 'D', 1, 1, 0, '2020-12-25 16:25:00'),
(3, 1, 2, 5, '123 + 345 + 723 =', '[1191,468,1181,1068]', 'A', 1, 1, 0, '2020-12-25 16:25:00'),
(4, 1, 3, 8, '<p>If 15 + 20 = 20 + n, n is equal to</p>\n', '[\"\\u003Cp\\u003E20\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E15\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E35\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E0\\u003C\\/p\\u003E\\n\"]', 'B', 1, 1, 0, '2020-12-25 16:25:00'),
(5, 1, 2, 5, 'If z + y = 20 and y = 5, what is z?', '[25,15,5,10]', 'B', 1, 1, 0, '2020-12-25 16:25:00'),
(6, 1, 2, 5, 'There are 15,768 people watching a game in a football stadium and there are 34,890 empty seats. What is the total number of seats in the stadium?', '[\"50,658\",\"49,558\",\"40,658\",\"50,558\"]', 'A', 1, 1, 0, '2020-12-25 16:25:00'),
(7, 1, 2, 5, 'The sum of z and y is equal to 125. If y = 45, which equation can be used to find z?', '[\"z - y = 125\",\"z + y = 45\",\" z - 45 = 125\",\"z + 45 = 125\"]', 'D', 1, 1, 0, '2020-12-25 16:25:00'),
(8, 1, 2, 5, 'A school spent $14589 on computers, $1234 on tables and $876 on chairs. How much money did the school spend?', '[15589,16699,16599,16589]', 'B', 1, 1, 0, '2020-12-25 16:25:00'),
(9, 1, 2, 5, 'Linda wrote a number that is two hundred sixty-five thousand, one hundred eight greater than thirty-two thousand, two hundred twenty-nine. What number did she wrote?', '[\"297,000\",\"297,300\",\"297,327\",\"297,337\"]', 'D', 1, 1, 0, '2020-12-25 16:25:00'),
(31, 1, 2, 4, '<p>dsdsdsads<span class=\"math-tex\">\\(x = {-b \\pm \\sqrt{b^2-4ac} \\over 2a}\\)</span></p>\n', '[\"\\u003Cp\\u003Edsdsads\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Edsdsadsa\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Edsadsad\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E dsdsad\\u003C\\/p\\u003E\\n\"]', 'B', 1, 1, 0, '2021-01-23 15:14:15'),
(32, 1, 2, 2, '<p><span class=\"math-tex\">\\(\\cos (2\\theta) = \\cos^2 \\theta - \\sin^2 \\theta\\)</span></p>\n', '[\"\\u003Cp\\u003E\\u003Cspan class=\\u0022math-tex\\u0022\\u003E\\\\(\\\\sin^2(\\\\theta)+ \\\\cos^2( \\\\theta)=0\\\\)\\u003C\\/span\\u003E\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E45\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E789789 fddsfds\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E8787\\u003C\\/p\\u003E\\n\"]', 'B', 1, 1, 0, '2021-01-23 16:05:24'),
(35, 1, 2, 2, '<p><span class=\"math-tex\">\\(\\cos (2\\theta) = \\cos^2 \\theta - \\sin^2 \\theta\\)</span> dddddddddd</p>\n', '[\"\\u003Cpre\\u003E\\n\\u003Cspan class=\\u0022math-tex\\u0022\\u003E\\\\(\\\\cos (2\\\\theta) = \\\\cos^2 \\\\theta - \\\\sin^2 \\\\theta\\\\)\\u003C\\/span\\u003E\\n\\u003C\\/pre\\u003E\\n\",\"\\u003Cp\\u003E\\u003Cspan class=\\u0022math-tex\\u0022\\u003E\\\\(x = {-b \\\\pm \\\\sqrt{b^2-4ac} \\\\over 2a}\\\\)\\u003C\\/span\\u003E\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Esss\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Essss\\u003C\\/p\\u003E\\n\"]', 'B', 1, 1, 0, '2021-01-27 15:18:55'),
(36, 1, 3, 7, '<p>ddsads<span class=\"math-tex\">\\(x = {-b \\pm \\sqrt{b^2-4ac} \\over 2a}\\)</span></p>\n', '[\"\\u003Cp\\u003Edsdsa dsadasdsa\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Edsdsa\\u003Cspan class=\\u0022math-tex\\u0022\\u003E\\\\(x = {-b \\\\pm \\\\sqrt{b^2-4ac} \\\\over 2a}\\\\)\\u003C\\/span\\u003E\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Edsads\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Edsadsad\\u003C\\/p\\u003E\\n\"]', 'B', 4, 1, 0, '2021-01-28 12:05:09'),
(37, 1, 2, 4, '<p>On dividing a number by 357, we get 39 as remainder. On dividing the same number 17, what will be the remainder ?</p>\n', '[\"\\u003Cp\\u003E0\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E3\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E5\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E11\\u003C\\/p\\u003E\\n\"]', 'C', 1, 1, 0, '2021-02-01 18:02:30'),
(39, 1, 2, 4, '<p>Which one of the following is the common factor of (4743 + 4343) and (4747 + 4347) ?</p>\n', '[\"\\u003Cp\\u003E(47 - 43)\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E(47 + 43)\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E(4743 + 4343)\\u003Cbr\\u003E\\n \\u003C\\/p\\u003E\\n\\n\\u003Cp\\u003E \\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003ENone of these\\u003C\\/p\\u003E\\n\"]', 'B', 1, 1, 2, '2021-02-01 18:04:26'),
(43, 1, 5, 20, '<p>The working principle of a washing machine is?</p>\n', '[\"\\u003Cp\\u003Ereverse osmosis\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Ediffusion\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Ecentrifugation\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Edialysis\\u003C\\/p\\u003E\\n\"]', 'C', 1, 1, 1, '2021-02-16 18:05:14'),
(44, 1, 5, 20, '<p>Nuclear sizes are expressed in a unit named</p>\n', '[\"\\u003Cp\\u003EFermi\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003EAngstrom\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E Newton\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003ETesla\\u003C\\/p\\u003E\\n\"]', 'A', 1, 1, 1, '2021-02-16 18:06:27'),
(45, 1, 5, 20, '<p>The speed of light will be minimum while passing through</p>\n', '[\"\\u003Cp\\u003Ewater\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Evaccum\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E air\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E glass\\u003C\\/p\\u003E\\n\"]', 'D', 1, 1, 2, '2021-02-16 18:09:09'),
(46, 1, 5, 20, '<p>Which of the following is not a vector quantity?</p>\n', '[\"\\u003Cp\\u003E speed\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Evelocity\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003E torque\\u003C\\/p\\u003E\\n\",\"\\u003Cp\\u003Edisplacement\\u003C\\/p\\u003E\\n\"]', 'A', 1, 1, 1, '2021-02-16 18:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `site_details`
--

CREATE TABLE `site_details` (
  `id` int(11) NOT NULL,
  `site_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `site_favicon` varchar(255) NOT NULL,
  `site_logo` varchar(255) NOT NULL,
  `site_loader` varchar(255) NOT NULL,
  `site_author` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `site_keywords` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `site_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `enrollment_word` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `copyright_text` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timezone` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `site_details`
--

INSERT INTO `site_details` (`id`, `site_title`, `site_favicon`, `site_logo`, `site_loader`, `site_author`, `site_keywords`, `site_description`, `enrollment_word`, `copyright_text`, `timezone`) VALUES
(1, 'E Academy', 'fav.png', 'logoq.svg', 'e-academy.gif', 'Kamlesh Yadav', 'e academy,academy,education academy', 'Description about e-academy', 'ACAD', 'Copyright © 2020 E Academy. All Right Reserved.', 'Asia/Kolkata');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `enrollment_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `contact_no` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `father_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `father_designtn` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `batch_id` int(11) NOT NULL,
  `login_status` int(11) NOT NULL,
  `admission_date` date NOT NULL,
  `status` int(11) NOT NULL,
  `payment_status` int(11) NOT NULL COMMENT '(0 unpaid ) (1 paid)',
  `brewers_check` varchar(50) NOT NULL,
  `token` varchar(500) NOT NULL,
  `app_version` varchar(100) NOT NULL,
  `added_by` varchar(50) NOT NULL,
  `last_login_app` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `admin_id`, `name`, `enrollment_id`, `password`, `image`, `email`, `contact_no`, `gender`, `dob`, `father_name`, `father_designtn`, `address`, `batch_id`, `login_status`, `admission_date`, `status`, `payment_status`, `brewers_check`, `token`, `app_version`, `added_by`, `last_login_app`) VALUES
(1, 1, 'om', 'ACAD1097', '202cb962ac59075b964b07152d234b70', 'img_600x400_jpg_201023160931_210101145554.jpg', 'omprakash.jha@pixelnx.com', '8319991921', 'male', '2000-01-11', 'John', 'John', 'John', 3, 1, '2020-12-30', 1, 1, 'syETQbUKcC', '1', '7', 'student', '2021-01-30 17:36:50'),
(25, 1, 'Selina', 'ACAD12491', '202cb962ac59075b964b07152d234b70', 'teenager-1887364_960_720_201023141320_210101150030.jpg', 'selina@eacademy.com', '9632587410', 'male', '2007-01-17', 'Symond', 'Farmer', 'California', 3, 1, '2021-01-01', 1, 0, 'Mf035GryAw', '1', '', '', '0000-00-00 00:00:00'),
(24, 1, 'sophia', 'ACAD12331', '202cb962ac59075b964b07152d234b70', 'scholarship-759_201023161243_210101113559.jpg', 'sophia@eacademy.com', '9632587410', 'female', '2001-01-01', 'Denial', 'Barber', 'California', 3, 1, '2021-01-01', 1, 0, '5eTXjNrBuO', '1', '5', '', '2021-01-01 12:03:55');

-- --------------------------------------------------------

--
-- Table structure for table `student_doubts_class`
--

CREATE TABLE `student_doubts_class` (
  `doubt_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `subjects_id` varchar(100) NOT NULL,
  `chapters_id` varchar(500) NOT NULL,
  `users_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teacher_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(100) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL COMMENT '0 = pending, 1 = approve, 2 = decline',
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student_doubts_class`
--

INSERT INTO `student_doubts_class` (`doubt_id`, `student_id`, `teacher_id`, `batch_id`, `subjects_id`, `chapters_id`, `users_description`, `teacher_description`, `appointment_date`, `appointment_time`, `create_at`, `status`, `admin_id`) VALUES
(5, 1, 4, 3, '3', '6', 'hello, Can you please explain periodic table of block carbon family for test preparation.', 'ssss', '2021-02-26', '6:30 PM', '2020-12-31 13:55:42', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `student_payment_history`
--

CREATE TABLE `student_payment_history` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `transaction_id` longtext NOT NULL,
  `amount` int(11) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_payment_history`
--

INSERT INTO `student_payment_history` (`id`, `student_id`, `batch_id`, `transaction_id`, `amount`, `create_at`) VALUES
(1, 26, 3, '91C25147KC549184X', 500, '2021-01-06 18:51:19'),
(2, 27, 3, '5XL15462U7328520S', 500, '2021-01-06 18:53:49'),
(3, 28, 3, '5XL15462U7328520S', 500, '2021-01-06 19:07:54'),
(4, 29, 3, '5T170739R4055030D', 500, '2021-01-06 19:09:18'),
(5, 30, 4, '75M55539E08821019', 50, '2021-01-07 12:03:39'),
(6, 31, 4, '3VU261501P345750V', 50, '2021-01-07 12:04:49'),
(7, 32, 4, '03H95585T11754458', 50, '2021-01-07 12:19:21'),
(8, 33, 4, '03H95585T11754458', 50, '2021-01-07 12:19:46'),
(9, 34, 4, '4FV63021WJ435871G', 50, '2021-01-07 12:22:27'),
(10, 35, 3, '7X046781304648155', 500, '2021-01-07 12:58:32'),
(11, 36, 3, '29S28341K6490441E', 500, '2021-01-07 14:58:33'),
(12, 37, 3, 'pay_GMi44akupSKXzq', 500, '2021-01-08 11:44:15'),
(13, 38, 3, 'pay_GMkwi5VUdnRMQX', 500, '2021-01-08 14:33:21'),
(14, 46, 3, 'pay_GXUokQJAn26k4T', 500, '2021-02-04 17:55:35'),
(15, 49, 3, '0VA37123SD6681411', 0, '2021-02-16 11:53:02'),
(16, 50, 10, '0SB70146K1980394P', 0, '2021-02-16 12:47:07'),
(17, 51, 10, '{\"mc_gross\":\"15.00\",\"protection_eligibility\":\"Ineligible\",\"address_status\":\"confirmed\",\"payer_id\":\"VGG548WL7XVC2\",\"address_street\":\"dhnsfhkdsj\\r\\njhkhkj\",\"payment_date\":\"23:33:50 Feb 15, 2021 PST\",\"payment_status\":\"Completed\",\"charset\":\"UTF-8\",\"address_zip\":\"455001\",\"first_name\":\"test\",\"mc_fee\":\"0.60\",\"address_country_code\":\"IN\",\"address_name\":\"test testt\",\"notify_version\":\"3.9\",\"custom\":\"astha sharma,astha.ofdifodiofd@pixelnx.com,07987101787\",\"payer_status\":\"verified\",\"business\":\"sb-047dxo4550137@personal.example.com\",\"address_country\":\"India\",\"address_city\":\"dewas\",\"quantity\":\"1\",\"verify_sign\":\"AD1jRi-l2MXSGu3f0.MZYRAAJ1ImAdDcTpHRrZEv4hDp1dF-JDtstoy1\",\"payer_email\":\"sachin@test.com\",\"txn_id\":\"1EJ812448L3897042\",\"payment_type\":\"instant\",\"last_name\":\"testt\",\"address_state\":\"MADHYA PRADESH\",\"receiver_email\":\"sb-047dxo4550137@personal.example.com\",\"payment_fee\":\"\",\"shipping_discount\":\"0.00\",\"insurance_amount\":\"0.00\",\"receiver_id\":\"GGLBM4XJZHGZW\",\"txn_type\":\"web_accept\",\"item_name\":\"Physics\",\"discount\":\"0.00\",\"mc_currency\":\"INR\",\"item_number\":\"10\",\"residence_country\":\"IN\",\"test_ipn\":\"1\",\"receipt_id\":\"2017-3844-5910-4571\",\"shipping_method\":\"Default\",\"transaction_subject\":\"\",\"payment_gross\":\"\",\"ipn_track_id\":\"465767943540d\"}', 0, '2021-02-16 13:03:59'),
(18, 52, 10, '496591954E459544X', 15, '2021-02-16 14:14:07'),
(19, 57, 10, '8WF82718V7685045P', 15, '2021-02-16 18:00:20'),
(20, 58, 10, 'pay_Gca07CtT5X6CDR', 15, '2021-02-17 14:20:12'),
(21, 59, 10, 'pay_GcawgPLnK1ZpIw', 15, '2021-02-17 15:11:18'),
(22, 60, 10, 'pay_GcbIHpeK2bLini', 15, '2021-02-17 15:30:52'),
(23, 61, 10, 'pay_GcbQpIFXxHOuOu', 15, '2021-02-17 15:38:53'),
(24, 62, 10, 'pay_GcbVQ6L6fuGZPv', 15, '2021-02-17 15:43:20'),
(25, 62, 10, 'pay_GcbVatGswiUn60', 15, '2021-02-17 15:43:20'),
(26, 64, 10, 'pay_GcbwjY30zqb71j', 15, '2021-02-17 16:09:16'),
(27, 64, 10, 'pay_GcbwzkfDchfczq', 15, '2021-02-17 16:09:16'),
(28, 65, 10, 'pay_Gcc4LyUilqeJE0', 15, '2021-02-17 16:16:41'),
(29, 65, 10, 'pay_Gcc4pUbiU37Xay', 15, '2021-02-17 16:16:41');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `no_of_questions` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `status`, `no_of_questions`, `admin_id`) VALUES
(2, 'Maths', 1, 36, 1),
(3, 'Chemistry', 1, 5, 1),
(5, 'Physics', 1, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sudent_batchs`
--

CREATE TABLE `sudent_batchs` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp(),
  `added_by` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sudent_batchs`
--

INSERT INTO `sudent_batchs` (`id`, `student_id`, `batch_id`, `status`, `create_at`, `added_by`) VALUES
(1, 23, 3, 0, '2021-01-01 10:57:40', 'student'),
(2, 26, 3, 0, '2021-01-06 18:51:19', 'student'),
(3, 27, 3, 0, '2021-01-06 18:53:49', 'student'),
(4, 28, 3, 0, '2021-01-06 19:07:54', 'student'),
(5, 29, 3, 0, '2021-01-06 19:09:18', 'student'),
(6, 30, 4, 0, '2021-01-07 12:03:39', 'student'),
(7, 31, 4, 0, '2021-01-07 12:04:49', 'student'),
(8, 32, 4, 0, '2021-01-07 12:19:21', 'student'),
(9, 33, 4, 0, '2021-01-07 12:19:46', 'student'),
(10, 34, 4, 0, '2021-01-07 12:22:27', 'student'),
(11, 35, 3, 0, '2021-01-07 12:58:32', 'student'),
(12, 36, 3, 0, '2021-01-07 14:58:34', 'student'),
(13, 37, 3, 0, '2021-01-08 11:44:15', 'student'),
(14, 38, 3, 0, '2021-01-08 14:33:22', 'student'),
(15, 39, 0, 0, '2021-01-08 15:44:49', 'student'),
(16, 40, 8, 0, '2021-01-08 15:47:44', 'student'),
(17, 41, 8, 0, '2021-01-08 15:49:53', 'student'),
(18, 43, 8, 0, '2021-01-29 14:34:42', 'student'),
(19, 43, 8, 0, '2021-01-29 14:34:42', 'student'),
(20, 45, 8, 0, '2021-02-01 16:54:54', 'student'),
(21, 46, 3, 0, '2021-02-04 17:55:35', 'student'),
(22, 47, 8, 0, '2021-02-08 17:38:53', 'student'),
(23, 48, 9, 0, '2021-02-13 15:54:12', 'student'),
(24, 49, 3, 0, '2021-02-16 11:53:02', 'student'),
(25, 50, 10, 0, '2021-02-16 12:47:07', 'student'),
(26, 51, 10, 0, '2021-02-16 13:03:59', 'student'),
(27, 52, 10, 0, '2021-02-16 14:14:07', 'student'),
(28, 53, 8, 0, '2021-02-16 14:31:56', 'student'),
(29, 54, 9, 0, '2021-02-16 14:46:33', 'student'),
(30, 55, 8, 0, '2021-02-16 14:49:18', 'student'),
(31, 56, 9, 0, '2021-02-16 14:53:16', 'student'),
(32, 56, 9, 0, '2021-02-16 14:53:16', 'student'),
(33, 57, 10, 0, '2021-02-16 18:00:20', 'student'),
(34, 58, 10, 0, '2021-02-17 14:20:12', 'student'),
(35, 59, 10, 0, '2021-02-17 15:11:18', 'student'),
(36, 60, 10, 0, '2021-02-17 15:30:52', 'student'),
(37, 61, 10, 0, '2021-02-17 15:38:53', 'student'),
(38, 62, 10, 0, '2021-02-17 15:43:20', 'student'),
(39, 62, 10, 0, '2021-02-17 15:43:20', 'student'),
(40, 63, 16, 0, '2021-02-17 15:54:05', 'student'),
(41, 63, 16, 0, '2021-02-17 15:54:05', 'student'),
(42, 64, 10, 0, '2021-02-17 16:09:16', 'student'),
(43, 64, 10, 0, '2021-02-17 16:09:16', 'student'),
(44, 65, 10, 0, '2021-02-17 16:16:41', 'student'),
(45, 65, 10, 0, '2021-02-17 16:16:41', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `temp_data`
--

CREATE TABLE `temp_data` (
  `id` int(11) NOT NULL,
  `temp` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `temp_data`
--

INSERT INTO `temp_data` (`id`, `temp`) VALUES
(1, '{\"exam_type\":\"practice\",\"paper_name\":\"Voilence\",\"start_time\":\"05:39:54 PM\",\"admin_id\":\"1\",\"student_id\":\"1\",\"total_question\":\"3\",\"time_duration\":\"10\",\"question_answer\":\"{\\\"30\\\":\\\"A\\\"}\",\"paper_id\":\"6\"}'),
(2, '{\"exam_type\":\"practice\",\"paper_name\":\"Voilence\",\"start_time\":\"10:49:38 AM\",\"admin_id\":\"1\",\"student_id\":\"1\",\"total_question\":\"3\",\"time_duration\":\"10\",\"question_answer\":\"{\\\"29\\\":\\\"A\\\",\\\"30\\\":\\\"B\\\",\\\"31\\\":\\\"D\\\"}\",\"paper_id\":\"6\"}'),
(3, '{\"exam_type\":\"practice\",\"paper_name\":\"Mathematics\",\"start_time\":\"03:36:31 PM\",\"admin_id\":\"1\",\"student_id\":\"43\",\"total_question\":\"10\",\"time_duration\":\"6\",\"question_answer\":\"{\\\"35\\\":\\\"B\\\",\\\"36\\\":\\\"B\\\",\\\"29\\\":\\\"B\\\",\\\"30\\\":\\\"A\\\",\\\"31\\\":\\\"C\\\",\\\"32\\\":\\\"A\\\"}\",\"paper_id\":\"7\"}'),
(4, '{\"exam_type\":\"practice\",\"paper_name\":\"data structure\",\"start_time\":\"11:57:18 AM\",\"admin_id\":\"1\",\"student_id\":\"43\",\"total_question\":\"10\",\"time_duration\":\"5\",\"question_answer\":\"{\\\"33\\\":\\\"B\\\",\\\"11\\\":\\\"B\\\",\\\"34\\\":\\\"A\\\",\\\"35\\\":\\\"A\\\",\\\"29\\\":\\\"A\\\",\\\"9\\\":\\\"A\\\",\\\"31\\\":\\\"A\\\",\\\"32\\\":\\\"A\\\"}\",\"paper_id\":\"8\"}'),
(5, '{\"exam_type\":\"practice\",\"paper_name\":\"data structure\",\"start_time\":\"06:20:16 PM\",\"admin_id\":\"1\",\"student_id\":\"43\",\"total_question\":\"10\",\"time_duration\":\"5\",\"question_answer\":\"{}\",\"paper_id\":\"8\"}'),
(6, '{\"exam_type\":\"practice\",\"paper_name\":\"data structure\",\"start_time\":\"10:19:07 AM\",\"admin_id\":\"1\",\"student_id\":\"43\",\"total_question\":\"1\",\"time_duration\":\"2\",\"question_answer\":\"{\\\"38\\\":\\\"A\\\"}\",\"paper_id\":\"9\"}'),
(7, '{\"exam_type\":\"practice\",\"paper_name\":\"1234\",\"start_time\":\"10:41:24 AM\",\"admin_id\":\"1\",\"student_id\":\"43\",\"total_question\":\"2\",\"time_duration\":\"5\",\"question_answer\":\"{\\\"38\\\":\\\"A\\\",\\\"40\\\":\\\"D\\\"}\",\"paper_id\":\"10\"}'),
(8, '{\"total_question\":\"3\",\"student_id\":\"45\",\"start_time\":\"11:16:27 AM\",\"admin_id\":\"1\",\"time_duration\":\"10\",\"paper_id\":\"6\",\"question_answer\":\"{\\\"30\\\":\\\"A\\\",\\\"31\\\":\\\"A\\\",\\\"29\\\":\\\"A\\\"}\",\"paper_name\":\"Voilence\",\"exam_type\":\"practice\"}'),
(9, '{\"total_question\":\"3\",\"student_id\":\"45\",\"start_time\":\"11:21:12 AM\",\"admin_id\":\"1\",\"time_duration\":\"10\",\"paper_id\":\"6\",\"question_answer\":\"{\\\"30\\\":\\\"B\\\",\\\"31\\\":\\\"D\\\"}\",\"paper_name\":\"Voilence\",\"exam_type\":\"practice\"}'),
(10, '{\"total_question\":\"3\",\"student_id\":\"45\",\"start_time\":\"11:23:59 AM\",\"admin_id\":\"1\",\"time_duration\":\"10\",\"paper_id\":\"6\",\"question_answer\":\"{\\\"30\\\":\\\"D\\\"}\",\"paper_name\":\"Voilence\",\"exam_type\":\"practice\"}'),
(11, '{\"total_question\":\"3\",\"student_id\":\"45\",\"start_time\":\"02:02:13 PM\",\"admin_id\":\"1\",\"time_duration\":\"10\",\"paper_id\":\"6\",\"question_answer\":\"{\\\"31\\\":\\\"A\\\"}\",\"paper_name\":\"Voilence\",\"exam_type\":\"practice\"}'),
(12, '{\"exam_type\":\"practice\",\"paper_name\":\"dfde\",\"start_time\":\"02:11:55 PM\",\"admin_id\":\"1\",\"student_id\":\"43\",\"total_question\":\"1\",\"time_duration\":\"2\",\"question_answer\":\"{\\\"40\\\":\\\"C\\\"}\",\"paper_id\":\"11\"}'),
(13, '{\"total_question\":\"12\",\"student_id\":\"45\",\"start_time\":\"02:15:20 PM\",\"admin_id\":\"1\",\"time_duration\":\"15\",\"paper_id\":\"12\",\"question_answer\":\"{\\\"1\\\":\\\"B\\\",\\\"8\\\":\\\"B\\\",\\\"39\\\":\\\"C\\\",\\\"4\\\":\\\"B\\\",\\\"7\\\":\\\"B\\\",\\\"5\\\":\\\"B\\\",\\\"11\\\":\\\"B\\\",\\\"9\\\":\\\"A\\\",\\\"3\\\":\\\"C\\\",\\\"2\\\":\\\"D\\\"}\",\"paper_name\":\"yhf\",\"exam_type\":\"practice\"}'),
(14, '{\"total_question\":\"10\",\"student_id\":\"45\",\"start_time\":\"02:27:26 PM\",\"admin_id\":\"1\",\"time_duration\":\"15\",\"paper_id\":\"13\",\"question_answer\":\"{}\",\"paper_name\":\"ghffg\",\"exam_type\":\"mock_test\"}'),
(15, '{\"total_question\":\"10\",\"student_id\":\"45\",\"start_time\":\"02:27:26 PM\",\"admin_id\":\"1\",\"time_duration\":\"15\",\"paper_id\":\"13\",\"question_answer\":\"{}\",\"paper_name\":\"ghffg\",\"exam_type\":\"mock_test\"}'),
(16, '{\"total_question\":\"8\",\"student_id\":\"45\",\"start_time\":\"02:27:46 PM\",\"admin_id\":\"1\",\"time_duration\":\"15\",\"paper_id\":\"14\",\"question_answer\":\"{\\\"8\\\":\\\"B\\\",\\\"39\\\":\\\"B\\\",\\\"40\\\":\\\"A\\\",\\\"6\\\":\\\"B\\\",\\\"7\\\":\\\"B\\\",\\\"5\\\":\\\"B\\\",\\\"4\\\":\\\"B\\\",\\\"3\\\":\\\"C\\\"}\",\"paper_name\":\"dads\",\"exam_type\":\"mock_test\"}'),
(17, '{\"total_question\":\"8\",\"student_id\":\"45\",\"start_time\":\"02:27:46 PM\",\"admin_id\":\"1\",\"time_duration\":\"15\",\"paper_id\":\"14\",\"question_answer\":\"{\\\"8\\\":\\\"B\\\",\\\"39\\\":\\\"B\\\",\\\"40\\\":\\\"A\\\",\\\"6\\\":\\\"B\\\",\\\"7\\\":\\\"B\\\",\\\"5\\\":\\\"B\\\",\\\"4\\\":\\\"B\\\",\\\"3\\\":\\\"C\\\"}\",\"paper_name\":\"dads\",\"exam_type\":\"mock_test\"}'),
(18, '{\"exam_type\":\"practice\",\"paper_name\":\"ghjg\",\"start_time\":\"02:59:30 PM\",\"admin_id\":\"1\",\"student_id\":\"43\",\"total_question\":\"1\",\"time_duration\":\"5\",\"question_answer\":\"{\\\"39\\\":\\\"B\\\"}\",\"paper_id\":\"16\"}'),
(19, '{\"exam_type\":\"practice\",\"paper_name\":\"yhf\",\"start_time\":\"02:59:46 PM\",\"admin_id\":\"1\",\"student_id\":\"43\",\"total_question\":\"12\",\"time_duration\":\"15\",\"question_answer\":\"{\\\"11\\\":\\\"A\\\",\\\"3\\\":\\\"B\\\",\\\"5\\\":\\\"B\\\",\\\"7\\\":\\\"B\\\",\\\"8\\\":\\\"A\\\"}\",\"paper_id\":\"12\"}'),
(20, '{\"exam_type\":\"practice\",\"paper_name\":\"yhf\",\"start_time\":\"03:21:38 PM\",\"admin_id\":\"1\",\"student_id\":\"43\",\"total_question\":\"12\",\"time_duration\":\"15\",\"question_answer\":\"{\\\"11\\\":\\\"B\\\",\\\"1\\\":\\\"D\\\",\\\"2\\\":\\\"D\\\",\\\"3\\\":\\\"A\\\",\\\"4\\\":\\\"B\\\",\\\"5\\\":\\\"D\\\",\\\"6\\\":\\\"B\\\",\\\"39\\\":\\\"B\\\",\\\"7\\\":\\\"D\\\",\\\"8\\\":\\\"B\\\",\\\"9\\\":\\\"C\\\",\\\"40\\\":\\\"B\\\"}\",\"paper_id\":\"12\"}'),
(21, '{\"exam_type\":\"practice\",\"paper_name\":\"dfde\",\"start_time\":\"04:44:20 PM\",\"admin_id\":\"1\",\"student_id\":\"43\",\"total_question\":\"1\",\"time_duration\":\"2\",\"question_answer\":\"{\\\"40\\\":\\\"A\\\"}\",\"paper_id\":\"11\"}');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `role` int(11) NOT NULL COMMENT '1 - admin, 3 - teacher',
  `teach_education` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teach_image` varchar(255) NOT NULL,
  `teach_batch` varchar(255) NOT NULL,
  `teach_subject` varchar(255) NOT NULL,
  `teach_gender` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `token` text NOT NULL,
  `brewers_check` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `teach_education`, `teach_image`, `teach_batch`, `teach_subject`, `teach_gender`, `parent_id`, `status`, `token`, `brewers_check`) VALUES
(1, 'admin', 'admin@eacademy.com', '202cb962ac59075b964b07152d234b70', 1, '', '', '', '', '', 0, 1, '1', 'Q1aHodh3ZX'),
(5, 'Alexa', 'alexa@eacademy.com', '202cb962ac59075b964b07152d234b70', 3, 'Master Of Mathematics ', 'teaching-website.jpeg', '3,10', '[\"5\",\"2\"]', 'female', 1, 1, '1', 'J0iEFeBbPY'),
(4, 'Diana', 'diana@eacademy.com', '202cb962ac59075b964b07152d234b70', 3, 'Master in Chemistry', 'Doubt-Clearing-Session.jpg', '4,3,16', '[\"3\"]', 'female', 1, 1, '1', 'rZ2oqv5UTa');

-- --------------------------------------------------------

--
-- Table structure for table `vacancy`
--

CREATE TABLE `vacancy` (
  `id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `last_date` date NOT NULL,
  `mode` varchar(255) NOT NULL,
  `files` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `added_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vacancy`
--

INSERT INTO `vacancy` (`id`, `title`, `description`, `start_date`, `last_date`, `mode`, `files`, `status`, `admin_id`, `added_at`) VALUES
(1, 'test upcoming', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '2020-12-30', '2021-01-04', 'Offline', '[\"ClickAd_Facebook_Ad_History.docx\"]', 1, 1, '2020-12-26 11:55:05'),
(2, 'Constable', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '2021-02-28', '2021-03-03', 'Online', '[\"cosmetic-footer-img-(1).png\"]', 1, 1, '2021-02-15 14:23:36'),
(3, 'Email Us', 'dsadsa', '2021-02-02', '2021-02-24', 'Offline', '', 1, 1, '2021-02-15 14:31:19'),
(4, 'fdfdddsfdsfd', 'fdsfsfds', '2021-02-25', '2021-02-28', 'Online', '', 1, 1, '2021-02-15 14:31:49');

-- --------------------------------------------------------

--
-- Table structure for table `video_lectures`
--

CREATE TABLE `video_lectures` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `batch` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `topic` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) NOT NULL,
  `video_type` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `added_by` int(11) NOT NULL,
  `added_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `video_lectures`
--

INSERT INTO `video_lectures` (`id`, `admin_id`, `title`, `batch`, `topic`, `subject`, `url`, `video_type`, `status`, `added_by`, `added_at`) VALUES
(15, 1, 'erewrew', '[\"4\"]', 'Organic', 'Chemistry', 'https://vimeo.com/473805509', 'vimeo', 1, 1, '2021-01-28 10:19:59'),
(16, 1, 'test', '[\"4\"]', 'Organic', 'Chemistry', 'https://www.metacafe.com/embed/12162060/russian-truckers-are-beyond-extreme/', 'embed', 1, 1, '2021-01-28 10:20:30'),
(17, 1, 'test', '[\"4\"]', 'Organic', 'Chemistry', 'https://dl.dropboxusercontent.com/s/ht46ayfjoywch11/big_buck_bunny_720p_2mb.mp4?dl=0', 'dropbox', 1, 1, '2021-01-28 10:21:18'),
(18, 1, 'erewrew', '[\"4\"]', 'Subtractions', 'Maths', 'https://dl.dropboxusercontent.com/s/ht46ayfjoywch11/big_buck_bunny_720p_2mb.mp4?dl=0', 'dropbox', 1, 1, '2021-01-28 10:35:51'),
(20, 1, 'test vimeovideo', '[\"4\"]', 'Organic', 'Chemistry', 'https://vimeo.com/5158741', 'vimeo', 1, 1, '2021-01-30 17:24:13'),
(14, 1, 'test', '[\"8\"]', 'Organic', 'Chemistry', 'https://www.youtube.com/watch?v=EngW7tLk6R8', 'youtube', 1, 1, '2021-01-28 10:19:28'),
(22, 1, 'ds', '[\"8\"]', 'History', 'Current Affaires', 'https://www.youtube.com/watch?v=tiLWCNFzThE&list=PLwGdqUZWnOp3aROg4wypcRhZqJG3ajZWJ&index=1', 'dropbox', 1, 1, '2021-02-01 16:20:06'),
(23, 1, 'u tube video', '[\"4\"]', 'Organic', 'Chemistry', 'https://www.youtube.com/watch?v=9xwazD5SyVg', 'youtube', 1, 1, '2021-02-03 10:03:49'),
(24, 1, 'Sum', '[\"3\"]', 'Addition', 'Maths', 'https://www.youtube.com/watch?v=UiGi2q7TqAQ', 'youtube', 1, 5, '2021-02-03 12:04:10'),
(26, 1, 'add', '[\"3\"]', 'Addition', 'Maths', 'https://www.youtube.com/watch?v=AQ7THUKx6Es', 'youtube', 1, 5, '2021-02-03 12:09:09'),
(27, 0, 'S block or family', '[\"4\"]', 'Periodic table', 'Chemistry', 'https://www.youtube.com/watch?v=zsUoWogVVeQ', 'youtube', 1, 0, '2021-02-03 12:23:57'),
(28, 1, 'S block or family', '[\"4\"]', 'Periodic table', 'Chemistry', 'https://www.youtube.com/watch?v=zsUoWogVVeQ', 'youtube', 1, 1, '2021-02-03 12:25:03'),
(29, 1, 'vimeo s block', '[\"4\"]', 'Periodic table', 'Chemistry', 'https://vimeo.com/130430682', 'vimeo', 1, 1, '2021-02-13 13:01:33'),
(30, 1, 'vimeo', '[\"3\"]', 'Multiplication', 'Maths', 'https://www.youtube.com/watch?v=AQ7THUKx6Es', 'youtube', 1, 5, '2021-02-03 12:38:30'),
(31, 1, 'vsdrp', '[\"3\"]', 'Multiplication', 'Maths', 'https://www.youtube.com/watch?v=AQ7THUKx6Es', 'youtube', 1, 5, '2021-02-03 12:39:40'),
(32, 1, 'P- block elements', '[\"4\"]', 'Periodic table', 'Chemistry', 'https://vimeo.com/501828450', 'vimeo', 1, 1, '2021-02-03 12:50:52'),
(33, 1, 'testing', '[\"4\"]', 'Periodic table', 'Chemistry', 'https://vimeo.com/501828450', 'vimeo', 1, 1, '2021-02-03 12:51:38'),
(34, 1, 'vimeo testt', '[\"4\"]', 'Inorganic', 'Chemistry', 'https://vimeo.com/130430682', 'vimeo', 1, 1, '2021-02-13 16:34:19'),
(35, 1, 'tom n jerry', '[\"4\"]', 'Inorganic', 'Chemistry', 'https://www.youtube.com/watch?v=3olM-9vcd4M', 'youtube', 1, 1, '2021-02-13 16:31:01'),
(36, 1, 'vimeo s block', '[\"4\"]', 'Inorganic', 'Chemistry', 'https://vimeo.com/130430682', 'vimeo', 1, 1, '2021-02-13 16:31:35'),
(37, 1, 'dropbox video', '[\"9\"]', 'Multiplication', 'Maths', 'https://dl.dropboxusercontent.com/s/ht46ayfjoywch11/big_buck_bunny_720p_2mb.mp4?dl=0', 'dropbox', 1, 5, '2021-02-13 17:07:37');

-- --------------------------------------------------------

--
-- Table structure for table `views_notification_student`
--

CREATE TABLE `views_notification_student` (
  `n_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `notice_type` varchar(100) NOT NULL,
  `views_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `views_notification_student`
--

INSERT INTO `views_notification_student` (`n_id`, `student_id`, `notice_type`, `views_time`) VALUES
(1, 1, 'videoLecture', '2021-01-30 17:37:00'),
(2, 1, 'homeWork', '2020-12-30 14:02:57'),
(3, 1, 'practicePaper', '2021-01-30 05:37:18'),
(15, 23, 'extraClass', '2021-01-01 11:00:03'),
(5, 1, 'vacancy', '2020-12-30 14:17:47'),
(22, 24, 'extraClass', '2021-01-01 12:29:13'),
(21, 24, 'videoLecture', '2021-01-01 12:40:06'),
(20, 23, 'homeWork', '2021-01-01 10:59:55'),
(19, 23, 'videoLecture', '2021-01-01 10:59:48'),
(18, 23, 'notices', '2021-01-01 10:59:22'),
(17, 23, 'vacancy', '2021-01-01 10:59:44'),
(16, 23, 'mockPaper', '2021-01-01 10:58:52'),
(23, 43, 'practicePaper', '2021-02-13 04:51:06'),
(24, 43, 'practicePaper', '2021-01-29 15:35:44'),
(25, 43, 'mockPaper', '2021-02-13 10:45:54'),
(26, 43, 'videoLecture', '2021-02-13 15:53:06'),
(27, 43, 'notices', '2021-02-13 13:01:44'),
(28, 43, 'vacancy', '2021-02-13 10:47:04'),
(29, 43, 'homeWork', '2021-02-03 15:40:34'),
(30, 43, 'extraClass', '2021-02-04 11:35:14'),
(31, 45, 'videoLecture', '2021-02-03 15:44:40'),
(32, 45, 'homeWork', '2021-02-03 12:22:04'),
(33, 45, 'practicePaper', '2021-02-03 02:54:53'),
(34, 45, 'vacancy', '2021-02-03 14:01:53'),
(35, 45, 'extraClass', '2021-02-03 15:44:32'),
(36, 45, 'notices', '2021-02-03 14:01:45'),
(37, 45, 'mockPaper', '2021-02-03 02:47:09');

-- --------------------------------------------------------

--
-- Table structure for table `zoom_api_credentials`
--

CREATE TABLE `zoom_api_credentials` (
  `id` int(11) NOT NULL,
  `android_api_key` varchar(250) NOT NULL,
  `android_api_secret` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `zoom_api_credentials`
--

INSERT INTO `zoom_api_credentials` (`id`, `android_api_key`, `android_api_secret`) VALUES
(1, 'xyz', 'xyz');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_versions`
--
ALTER TABLE `app_versions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `batch_fecherd`
--
ALTER TABLE `batch_fecherd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `batch_subjects`
--
ALTER TABLE `batch_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_comments_reply`
--
ALTER TABLE `blog_comments_reply`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certificate`
--
ALTER TABLE `certificate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certificate_setting`
--
ALTER TABLE `certificate_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `enquiry`
--
ALTER TABLE `enquiry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `extra_classes`
--
ALTER TABLE `extra_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `extra_class_attendance`
--
ALTER TABLE `extra_class_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `frontend_details`
--
ALTER TABLE `frontend_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `general_settings`
--
ALTER TABLE `general_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homeworks`
--
ALTER TABLE `homeworks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `leave_management`
--
ALTER TABLE `leave_management`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`teacher_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `live_class_history`
--
ALTER TABLE `live_class_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `live_class_setting`
--
ALTER TABLE `live_class_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mock_result`
--
ALTER TABLE `mock_result`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `practice_result`
--
ALTER TABLE `practice_result`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `privacy_policy_data`
--
ALTER TABLE `privacy_policy_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `site_details`
--
ALTER TABLE `site_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `student_doubts_class`
--
ALTER TABLE `student_doubts_class`
  ADD PRIMARY KEY (`doubt_id`);

--
-- Indexes for table `student_payment_history`
--
ALTER TABLE `student_payment_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `sudent_batchs`
--
ALTER TABLE `sudent_batchs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temp_data`
--
ALTER TABLE `temp_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `vacancy`
--
ALTER TABLE `vacancy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `video_lectures`
--
ALTER TABLE `video_lectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `views_notification_student`
--
ALTER TABLE `views_notification_student`
  ADD PRIMARY KEY (`n_id`);

--
-- Indexes for table `zoom_api_credentials`
--
ALTER TABLE `zoom_api_credentials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_versions`
--
ALTER TABLE `app_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `batch_fecherd`
--
ALTER TABLE `batch_fecherd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `batch_subjects`
--
ALTER TABLE `batch_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `blog_comments_reply`
--
ALTER TABLE `blog_comments_reply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `certificate`
--
ALTER TABLE `certificate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `certificate_setting`
--
ALTER TABLE `certificate_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enquiry`
--
ALTER TABLE `enquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `extra_classes`
--
ALTER TABLE `extra_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `extra_class_attendance`
--
ALTER TABLE `extra_class_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `frontend_details`
--
ALTER TABLE `frontend_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `general_settings`
--
ALTER TABLE `general_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `homeworks`
--
ALTER TABLE `homeworks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `leave_management`
--
ALTER TABLE `leave_management`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `live_class_history`
--
ALTER TABLE `live_class_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `live_class_setting`
--
ALTER TABLE `live_class_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mock_result`
--
ALTER TABLE `mock_result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `practice_result`
--
ALTER TABLE `practice_result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `privacy_policy_data`
--
ALTER TABLE `privacy_policy_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `site_details`
--
ALTER TABLE `site_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `student_doubts_class`
--
ALTER TABLE `student_doubts_class`
  MODIFY `doubt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `student_payment_history`
--
ALTER TABLE `student_payment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sudent_batchs`
--
ALTER TABLE `sudent_batchs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `temp_data`
--
ALTER TABLE `temp_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vacancy`
--
ALTER TABLE `vacancy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `video_lectures`
--
ALTER TABLE `video_lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `views_notification_student`
--
ALTER TABLE `views_notification_student`
  MODIFY `n_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `zoom_api_credentials`
--
ALTER TABLE `zoom_api_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
