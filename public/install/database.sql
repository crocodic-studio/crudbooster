SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `cms_apicustom` (
  `id` int(11) NOT NULL,
  `permalink` varchar(100) NOT NULL,
  `tabel` varchar(50) NOT NULL,
  `aksi` varchar(25) NOT NULL,
  `kolom` text NOT NULL,
  `orderby` varchar(35) DEFAULT NULL,
  `sub_query_1` text,
  `sub_query_2` text,
  `sub_query_3` text,
  `sql_where` text,
  `nama` varchar(255) NOT NULL,
  `keterangan` varchar(500) DEFAULT NULL,
  `parameter` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cms_companies` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(90) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `photo` varchar(60) NOT NULL,
  `description` varchar(255) NOT NULL,
  `latitude` varchar(155) DEFAULT NULL,
  `longitude` varchar(155) DEFAULT NULL,
  `is_primary` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_companies` (`id`, `created_at`, `name`, `address`, `phone`, `email`, `photo`, `description`, `latitude`, `longitude`, `is_primary`) VALUES(1, '2016-04-11 04:38:49', 'CRUDBooster', 'Lorem ipsum dolor sit amet', '122324234', 'support@crudbooster.com', '2016-08/49ddc420a7565204990e53d7fe072a50.jpg', 'Quisque ut nisi. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Phasellus magna. Suspendisse potenti. Curabitur ullamcorper ultricies nisi.', '-7.005145300000001', '110.43812539999999', 1);

CREATE TABLE `cms_dashboard` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `id_cms_privileges` int(11) NOT NULL,
  `content` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_dashboard` (`id`, `name`, `id_cms_privileges`, `content`) VALUES(3, 'Total Users', 3, 'a:9:{s:4:"type";s:16:"statistic_number";s:2:"id";s:1:"3";s:5:"label";s:11:"Total Users";s:4:"icon";s:18:"ion-person-stalker";s:5:"color";s:3:"red";s:10:"table_name";s:9:"cms_users";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";}');
INSERT INTO `cms_dashboard` (`id`, `name`, `id_cms_privileges`, `content`) VALUES(4, 'Total Company', 3, 'a:8:{s:4:"type";s:16:"statistic_number";s:2:"id";s:1:"4";s:5:"label";s:13:"Total Company";s:5:"color";s:6:"yellow";s:10:"table_name";s:13:"cms_companies";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";}');
INSERT INTO `cms_dashboard` (`id`, `name`, `id_cms_privileges`, `content`) VALUES(5, 'Total Articles', 3, 'a:9:{s:4:"type";s:16:"statistic_number";s:2:"id";s:1:"5";s:5:"label";s:14:"Total Articles";s:4:"icon";s:23:"ion-arrow-graph-up-left";s:5:"color";s:4:"aqua";s:10:"table_name";s:9:"cms_posts";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";}');
INSERT INTO `cms_dashboard` (`id`, `name`, `id_cms_privileges`, `content`) VALUES(6, 'Total Pages', 3, 'a:9:{s:4:"type";s:16:"statistic_number";s:2:"id";s:1:"6";s:5:"label";s:11:"Total Pages";s:4:"icon";s:23:"ion-arrow-graph-up-left";s:5:"color";s:5:"green";s:10:"table_name";s:9:"cms_pages";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";}');
INSERT INTO `cms_dashboard` (`id`, `name`, `id_cms_privileges`, `content`) VALUES(11, 'Articles By Date', 3, 'a:10:{s:4:"type";s:10:"chart_line";s:2:"id";s:2:"11";s:5:"label";s:16:"Articles By Date";s:5:"color";s:6:"yellow";s:5:"width";s:4:"half";s:10:"table_name";s:9:"cms_posts";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";s:12:"sql_group_by";s:27:"DATE(created_at) as tanggal";}');
INSERT INTO `cms_dashboard` (`id`, `name`, `id_cms_privileges`, `content`) VALUES(14, 'Articles Bar', 3, 'a:10:{s:4:"type";s:9:"chart_bar";s:2:"id";s:2:"14";s:5:"label";s:12:"Articles Bar";s:5:"color";s:5:"green";s:5:"width";s:4:"half";s:10:"table_name";s:9:"cms_posts";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";s:12:"sql_group_by";s:27:"date(created_at) as tanggal";}');

CREATE TABLE `cms_filemanager` (
  `id` int(11) NOT NULL,
  `id_md5` varchar(32) NOT NULL,
  `name` varchar(100) NOT NULL,
  `filedata` longblob NOT NULL,
  `ext` varchar(20) NOT NULL,
  `content_type` varchar(25) NOT NULL,
  `size` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cms_logs` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `ipaddress` varchar(45) NOT NULL,
  `useragent` varchar(150) NOT NULL,
  `url` varchar(150) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `id_cms_users` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_logs` (`id`, `created_at`, `ipaddress`, `useragent`, `url`, `description`, `id_cms_users`) VALUES(19, '2016-08-06 17:30:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'http://localhost:8000/admin/users/delete-image', 'Delete image for Administrator at Users', 13);
INSERT INTO `cms_logs` (`id`, `created_at`, `ipaddress`, `useragent`, `url`, `description`, `id_cms_users`) VALUES(20, '2016-08-06 17:31:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'http://localhost:8000/admin/users/edit-save/13', 'Update data Administrator at Users', 13);
INSERT INTO `cms_logs` (`id`, `created_at`, `ipaddress`, `useragent`, `url`, `description`, `id_cms_users`) VALUES(21, '2016-08-06 17:34:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'http://localhost:8000/admin/users/delete-image', 'Delete image for Administrator at Users', 13);
INSERT INTO `cms_logs` (`id`, `created_at`, `ipaddress`, `useragent`, `url`, `description`, `id_cms_users`) VALUES(22, '2016-08-06 17:35:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'http://localhost:8000/admin/users/edit-save/13', 'Update data Administrator at Users', 13);
INSERT INTO `cms_logs` (`id`, `created_at`, `ipaddress`, `useragent`, `url`, `description`, `id_cms_users`) VALUES(23, '2016-08-06 22:10:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'http://localhost:8000/admin/users/delete-image', 'Delete image for Administrator at Users', 13);
INSERT INTO `cms_logs` (`id`, `created_at`, `ipaddress`, `useragent`, `url`, `description`, `id_cms_users`) VALUES(24, '2016-08-06 22:11:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'http://localhost:8000/admin/users/edit-save/13', 'Update data Administrator at Users', 13);
INSERT INTO `cms_logs` (`id`, `created_at`, `ipaddress`, `useragent`, `url`, `description`, `id_cms_users`) VALUES(25, '2016-08-06 22:25:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'http://localhost:8000/admin/companies/edit-save/1', 'Update data CRUDBooster at Companies', 13);
INSERT INTO `cms_logs` (`id`, `created_at`, `ipaddress`, `useragent`, `url`, `description`, `id_cms_users`) VALUES(26, '2016-08-07 10:57:27', '::1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'http://localhost:8000/admin/settings/delete/14', 'Delete data upload_mode at Settings', 13);
INSERT INTO `cms_logs` (`id`, `created_at`, `ipaddress`, `useragent`, `url`, `description`, `id_cms_users`) VALUES(27, '2016-08-07 10:57:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'http://localhost:8000/admin/settings/delete/13', 'Delete data upload_path at Settings', 13);

CREATE TABLE `cms_menus` (
  `id` int(11) NOT NULL,
  `id_cms_menus_groups` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `menu_type` varchar(55) NOT NULL,
  `menu_link` varchar(255) DEFAULT NULL,
  `id_cms_pages` int(11) DEFAULT NULL,
  `id_cms_posts` int(11) NOT NULL,
  `id_cms_posts_categories` int(11) NOT NULL,
  `parent_id_cms_menus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_menus` (`id`, `id_cms_menus_groups`, `name`, `menu_type`, `menu_link`, `id_cms_pages`, `id_cms_posts`, `id_cms_posts_categories`, `parent_id_cms_menus`) VALUES(1, 1, 'Menu 1', 'Custom Link', 'http://crocodic.com', 0, 0, 0, 0);
INSERT INTO `cms_menus` (`id`, `id_cms_menus_groups`, `name`, `menu_type`, `menu_link`, `id_cms_pages`, `id_cms_posts`, `id_cms_posts_categories`, `parent_id_cms_menus`) VALUES(3, 1, 'Menu 1 2', 'Custom Link', 'http://crocodic.com', 0, 0, 0, 1);
INSERT INTO `cms_menus` (`id`, `id_cms_menus_groups`, `name`, `menu_type`, `menu_link`, `id_cms_pages`, `id_cms_posts`, `id_cms_posts_categories`, `parent_id_cms_menus`) VALUES(5, 1, 'Menu 1 3', 'Custom Link', 'http://crocodic.com', 0, 0, 0, 1);

CREATE TABLE `cms_menus_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_menus_groups` (`id`, `name`, `slug`) VALUES(1, 'Menu Top', 'menu_top');

CREATE TABLE `cms_moduls` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `name` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `path` varchar(100) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `controller` varchar(100) DEFAULT NULL,
  `sql_where` varchar(500) DEFAULT NULL,
  `sql_orderby` varchar(255) DEFAULT NULL,
  `sorting` int(11) NOT NULL,
  `limit_data` int(11) DEFAULT NULL,
  `id_cms_moduls_group` int(11) DEFAULT NULL,
  `is_softdelete` int(1) NOT NULL,
  `is_active` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(1, '2016-06-24 09:17:38', '2016-02-25 15:22:16', 'Privileges', 'fa fa-cog', 'admin/privileges', '', 'PrivilegesController', '', '', 6, NULL, 9, 0, 1);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(2, '2016-06-24 10:10:51', '2016-02-25 15:22:26', 'Moduls', 'fa fa-cog', 'admin/cms_moduls', 'cms_moduls', 'ModulsController', '', '', 2, NULL, 9, 0, 0);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(3, '2016-06-25 23:14:28', '2016-03-01 06:45:11', 'Users', 'fa fa-users', 'admin/users', '', 'UsersController', '', '', 1, NULL, 2, 0, 1);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(4, '2016-06-24 09:17:38', '2016-02-25 15:22:48', 'Privileges Roles', 'fa fa-cog', 'admin/privileges_roles', '', 'PrivilegesRolesController', '', '', 8, NULL, 9, 0, 0);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(5, '2016-06-24 09:17:38', '2016-03-01 06:45:43', 'Settings', 'fa fa-cog', 'admin/settings', '', 'SettingsController', '', '', 5, NULL, 9, 0, 1);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(15, '2016-08-04 16:53:36', '2016-03-08 14:55:11', 'Module Generator', 'fa fa-database', 'admin/cms_moduls_group', '', 'ModulsGroupController', '', '', 1, 0, 9, 0, 1);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(41, '2016-06-24 09:17:38', '2016-03-08 16:17:47', 'API Generator', 'fa fa-cloud-download', 'admin/api_generator', '', 'ApiGeneratorController', '', '', 3, NULL, 9, 0, 1);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(81, '2016-06-24 10:22:07', '0000-00-00 00:00:00', 'API Management', 'fa fa-dashboard', 'admin/api_management', '', 'ApiCustomController', '', '', 4, NULL, 9, 0, 0);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(82, '2016-06-25 23:14:28', '0000-00-00 00:00:00', 'Companies', 'fa fa-bank', 'admin/companies', '', 'CompaniesController', '', '', 2, NULL, 2, 0, 1);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(83, '2016-06-24 09:17:38', '0000-00-00 00:00:00', 'Logs', 'fa fa-flag-o', 'admin/logs', '', 'LogsController', '', '', 7, NULL, 9, 0, 1);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(86, '2016-06-25 23:26:41', '0000-00-00 00:00:00', 'Menu Group', 'fa fa-bars', 'admin/cms_menus_groups', 'cms_menus_groups', 'AdminCmsMenusGroupsController', '', '', 9, 0, 9, 0, 1);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(87, '2016-06-25 23:26:54', '0000-00-00 00:00:00', 'Menu', 'fa fa-bars', 'admin/cms_menus', 'cms_menus', 'AdminCmsMenusController', '', '', 10, 0, 9, 0, 0);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(90, '2016-08-04 17:35:30', '0000-00-00 00:00:00', 'List Articles', 'fa fa-bars', 'admin/cms_posts', 'cms_posts', 'AdminCmsPostsController', '', '', 1, 20, 10, 1, 1);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(91, '2016-06-25 23:46:33', '0000-00-00 00:00:00', 'Categories', 'fa fa-database', 'admin/cms_posts_categories', 'cms_posts_categories', 'AdminCmsPostsCategoriesController', '', '', 2, 0, 10, 0, 1);
INSERT INTO `cms_moduls` (`id`, `created_at`, `updated_at`, `name`, `icon`, `path`, `table_name`, `controller`, `sql_where`, `sql_orderby`, `sorting`, `limit_data`, `id_cms_moduls_group`, `is_softdelete`, `is_active`) VALUES(92, '2016-08-04 17:36:38', '0000-00-00 00:00:00', 'Pages', 'fa fa-info-circle', 'admin/cms_pages', 'cms_pages', 'AdminCmsPagesController', '', '', 1, 20, 1, 1, 1);

CREATE TABLE `cms_moduls_group` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `nama_group` varchar(40) NOT NULL,
  `sorting_group` int(11) NOT NULL,
  `is_group` int(1) NOT NULL DEFAULT '1',
  `icon_group` varchar(35) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_moduls_group` (`id`, `created_at`, `updated_at`, `nama_group`, `sorting_group`, `is_group`, `icon_group`) VALUES(1, '2016-08-04 16:54:11', '2016-03-01 07:03:13', 'Public', 1, 0, 'fa fa-bars');
INSERT INTO `cms_moduls_group` (`id`, `created_at`, `updated_at`, `nama_group`, `sorting_group`, `is_group`, `icon_group`) VALUES(2, '2016-06-26 08:10:05', '2016-02-25 15:29:30', 'Master Data', 3, 1, 'fa fa-database');
INSERT INTO `cms_moduls_group` (`id`, `created_at`, `updated_at`, `nama_group`, `sorting_group`, `is_group`, `icon_group`) VALUES(9, '2016-06-26 08:10:14', '0000-00-00 00:00:00', 'Setting', 4, 1, 'fa fa-cog');
INSERT INTO `cms_moduls_group` (`id`, `created_at`, `updated_at`, `nama_group`, `sorting_group`, `is_group`, `icon_group`) VALUES(10, '2016-06-26 08:11:44', '0000-00-00 00:00:00', 'Articles', 2, 1, 'fa fa-bars');

CREATE TABLE `cms_pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_pages` (`id`, `title`, `slug`, `created_at`, `content`, `deleted_at`) VALUES(1, 'Curabitur suscipit suscipit tellus', 'curabitur-suscipit-suscipit-tellus', '2016-06-25 23:56:21', '<p>Curabitur suscipit suscipit tellus</p>', NULL);
INSERT INTO `cms_pages` (`id`, `title`, `slug`, `created_at`, `content`, `deleted_at`) VALUES(2, 'Suspendisse nisl elit rhoncus', 'suspendisse-nisl-elit-rhoncus', '2016-06-25 23:56:35', '<p>Suspendisse nisl elit rhoncus</p>', NULL);

CREATE TABLE `cms_posts` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `id_cms_users` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `id_cms_posts_categories` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_posts` (`id`, `created_at`, `title`, `content`, `id_cms_users`, `slug`, `id_cms_posts_categories`, `deleted_at`) VALUES(1, '2016-06-25 10:00:00', 'Vivamus aliquet elit ac', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla augue nunc, efficitur vitae pharetra ut, ullamcorper et justo. Vestibulum vulputate semper mi nec pellentesque. Suspendisse ultricies sagittis nisl quis scelerisque. Sed eleifend lorem id ligula mattis lacinia. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam tincidunt eleifend nibh ac consequat. Donec id mattis orci. Aliquam semper, erat congue consectetur pulvinar, odio augue hendrerit dui, vel facilisis est orci vel nisl. Quisque vehicula tempor felis, non commodo nunc blandit pellentesque. Cras tempor eros eu massa suscipit, congue elementum lorem luctus.</p>\r\n<p>Sed quis justo sem. Pellentesque quis nisl sit amet nisi condimentum consectetur. Quisque pellentesque velit malesuada malesuada lacinia. Phasellus dapibus purus in arcu congue vestibulum. Pellentesque congue urna vitae eleifend feugiat. Nam libero tortor, finibus vitae urna quis, fringilla fringilla sapien. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam nibh massa, aliquam sit amet sapien ut, finibus elementum urna. Vestibulum fringilla velit ac ipsum fringilla, sed hendrerit dolor ornare. Mauris id sollicitudin dui.</p>\r\n<p>Quisque faucibus sapien sed iaculis commodo. Quisque gravida nunc porta consequat porta. Ut augue nulla, ultricies ac fermentum in, mattis sit amet ipsum. Duis cursus turpis eget finibus dictum. Suspendisse condimentum ante felis, viverra lacinia mi consectetur sed. Donec iaculis, turpis at vulputate volutpat, lacus mi mattis erat, tincidunt rutrum lectus nisi id mauris. Suspendisse aliquet, nisi ac mattis hendrerit, ipsum mauris congue dolor, quis vestibulum arcu ipsum sit amet mi. Suspendisse sit amet finibus nisi. Nunc quis finibus purus. Nam laoreet ex sed magna placerat porttitor.</p>', 13, 'vivamus-aliquet-elit-ac', 1, NULL);
INSERT INTO `cms_posts` (`id`, `created_at`, `title`, `content`, `id_cms_users`, `slug`, `id_cms_posts_categories`, `deleted_at`) VALUES(2, '2016-06-25 10:00:00', 'Nam pretium turpis et In hac habitasse platea', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla augue nunc, efficitur vitae pharetra ut, ullamcorper et justo. Vestibulum vulputate semper mi nec pellentesque. Suspendisse ultricies sagittis nisl quis scelerisque. Sed eleifend lorem id ligula mattis lacinia. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam tincidunt eleifend nibh ac consequat. Donec id mattis orci. Aliquam semper, erat congue consectetur pulvinar, odio augue hendrerit dui, vel facilisis est orci vel nisl. Quisque vehicula tempor felis, non commodo nunc blandit pellentesque. Cras tempor eros eu massa suscipit, congue elementum lorem luctus.</p>\r\n<p>Sed quis justo sem. Pellentesque quis nisl sit amet nisi condimentum consectetur. Quisque pellentesque velit malesuada malesuada lacinia. Phasellus dapibus purus in arcu congue vestibulum. Pellentesque congue urna vitae eleifend feugiat. Nam libero tortor, finibus vitae urna quis, fringilla fringilla sapien. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam nibh massa, aliquam sit amet sapien ut, finibus elementum urna. Vestibulum fringilla velit ac ipsum fringilla, sed hendrerit dolor ornare. Mauris id sollicitudin dui.</p>\r\n<p>Quisque faucibus sapien sed iaculis commodo. Quisque gravida nunc porta consequat porta. Ut augue nulla, ultricies ac fermentum in, mattis sit amet ipsum. Duis cursus turpis eget finibus dictum. Suspendisse condimentum ante felis, viverra lacinia mi consectetur sed. Donec iaculis, turpis at vulputate volutpat, lacus mi mattis erat, tincidunt rutrum lectus nisi id mauris. Suspendisse aliquet, nisi ac mattis hendrerit, ipsum mauris congue dolor, quis vestibulum arcu ipsum sit amet mi. Suspendisse sit amet finibus nisi. Nunc quis finibus purus. Nam laoreet ex sed magna placerat porttitor.</p>', 13, 'nam-pretium-turpis-et-in-hac-habitasse-platea', 2, NULL);
INSERT INTO `cms_posts` (`id`, `created_at`, `title`, `content`, `id_cms_users`, `slug`, `id_cms_posts_categories`, `deleted_at`) VALUES(3, '2016-06-26 00:03:33', 'Morbi vestibulum volutpat enim', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla augue nunc, efficitur vitae pharetra ut, ullamcorper et justo. Vestibulum vulputate semper mi nec pellentesque. Suspendisse ultricies sagittis nisl quis scelerisque. Sed eleifend lorem id ligula mattis lacinia. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam tincidunt eleifend nibh ac consequat. Donec id mattis orci. Aliquam semper, erat congue consectetur pulvinar, odio augue hendrerit dui, vel facilisis est orci vel nisl. Quisque vehicula tempor felis, non commodo nunc blandit pellentesque. Cras tempor eros eu massa suscipit, congue elementum lorem luctus.</p>\r\n<p>Sed quis justo sem. Pellentesque quis nisl sit amet nisi condimentum consectetur. Quisque pellentesque velit malesuada malesuada lacinia. Phasellus dapibus purus in arcu congue vestibulum. Pellentesque congue urna vitae eleifend feugiat. Nam libero tortor, finibus vitae urna quis, fringilla fringilla sapien. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam nibh massa, aliquam sit amet sapien ut, finibus elementum urna. Vestibulum fringilla velit ac ipsum fringilla, sed hendrerit dolor ornare. Mauris id sollicitudin dui.</p>\r\n<p>Quisque faucibus sapien sed iaculis commodo. Quisque gravida nunc porta consequat porta. Ut augue nulla, ultricies ac fermentum in, mattis sit amet ipsum. Duis cursus turpis eget finibus dictum. Suspendisse condimentum ante felis, viverra lacinia mi consectetur sed. Donec iaculis, turpis at vulputate volutpat, lacus mi mattis erat, tincidunt rutrum lectus nisi id mauris. Suspendisse aliquet, nisi ac mattis hendrerit, ipsum mauris congue dolor, quis vestibulum arcu ipsum sit amet mi. Suspendisse sit amet finibus nisi. Nunc quis finibus purus. Nam laoreet ex sed magna placerat porttitor.</p>', 13, 'morbi-vestibulum-volutpat-enim', 1, NULL);

CREATE TABLE `cms_posts_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_posts_categories` (`id`, `name`, `slug`) VALUES(1, 'General', 'general');
INSERT INTO `cms_posts_categories` (`id`, `name`, `slug`) VALUES(2, 'Other', 'other');

CREATE TABLE `cms_privileges` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `name` varchar(50) NOT NULL,
  `is_superadmin` int(1) NOT NULL,
  `filter_field` varchar(25) DEFAULT NULL,
  `filter_value` varchar(25) DEFAULT NULL,
  `theme_color` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_privileges` (`id`, `created_at`, `updated_at`, `name`, `is_superadmin`, `filter_field`, `filter_value`, `theme_color`) VALUES(3, '2016-05-22 23:46:06', '0000-00-00 00:00:00', 'Superadmin', 1, '', NULL, 'skin-blue-light');

CREATE TABLE `cms_privileges_roles` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_visible` int(1) NOT NULL DEFAULT '1',
  `is_create` int(1) NOT NULL,
  `is_read` int(1) NOT NULL,
  `is_edit` int(1) NOT NULL,
  `is_delete` int(1) NOT NULL,
  `id_cms_privileges` int(11) NOT NULL,
  `id_cms_moduls` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(869, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 41);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(870, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 81);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(871, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 91);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(872, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 82);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(873, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 90);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(874, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 0, 1, 0, 1, 3, 83);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(875, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 87);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(876, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 86);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(877, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 15);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(878, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 2);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(879, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 92);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(880, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 1);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(881, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 0, 1, 1, 3, 4);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(882, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 5);
INSERT INTO `cms_privileges_roles` (`id`, `created_at`, `updated_at`, `is_visible`, `is_create`, `is_read`, `is_edit`, `is_delete`, `id_cms_privileges`, `id_cms_moduls`) VALUES(883, '2016-08-04 17:27:18', '0000-00-00 00:00:00', 1, 1, 1, 1, 1, 3, 3);

CREATE TABLE `cms_settings` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `name` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `content_input_type` varchar(35) NOT NULL DEFAULT 'textarea',
  `dataenum` varchar(355) DEFAULT NULL,
  `helper` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(1, '2016-06-13 12:34:16', '2016-03-18 11:53:07', 'appname', 'CRUDBooster', 'text', NULL, NULL);
INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(2, '2016-07-27 09:52:37', '2016-02-25 11:34:24', 'email_sender', 'support@crudbooster.com', 'text', NULL, NULL);
INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(18, '2016-06-13 12:34:51', '0000-00-00 00:00:00', 'app_lockscreen_timeout', '60', 'text', NULL, 'in number of seconds');
INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(19, '2016-06-13 12:35:12', '0000-00-00 00:00:00', 'default_paper_size', 'Legal', 'text', NULL, 'Paper Size ex : A4, Legal, Letter');
INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(20, '2016-07-27 09:51:06', '0000-00-00 00:00:00', 'smtp_host', '', 'text', NULL, 'host of smtp');
INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(21, '2016-06-13 12:35:34', '0000-00-00 00:00:00', 'smtp_port', '25', 'text', NULL, 'default 25');
INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(22, '2016-07-27 09:51:14', '0000-00-00 00:00:00', 'smtp_username', '', 'textarea', NULL, NULL);
INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(23, '2016-04-16 21:12:12', '0000-00-00 00:00:00', 'smtp_password', '123456', 'textarea', NULL, NULL);
INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(24, '2016-07-27 09:52:24', '0000-00-00 00:00:00', 'logo', '', 'upload', NULL, 'Upload Image jpg,png,gif , Max Size 500 KB');
INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(25, '2016-07-27 09:51:56', '0000-00-00 00:00:00', 'favicon', '', 'upload', NULL, 'File support jpg,png,gif . Max size 300 KB. Resolution 120x120');
INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(29, '2016-06-24 09:48:47', '0000-00-00 00:00:00', 'api_debug_mode', 'true', 'radio', 'true,false', NULL);
INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(30, '2016-07-27 09:51:23', '0000-00-00 00:00:00', 'smtp_driver', 'mail', 'select', 'smtp,mail,sendmail', '');
INSERT INTO `cms_settings` (`id`, `created_at`, `updated_at`, `name`, `content`, `content_input_type`, `dataenum`, `helper`) VALUES(31, '2016-07-27 09:52:11', '0000-00-00 00:00:00', 'google_api_key', '', 'text', '', 'Enter google api key for maps');

CREATE TABLE `cms_users` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `name` varchar(50) NOT NULL,
  `photo` varchar(60) NOT NULL,
  `email` varchar(25) NOT NULL,
  `password` varchar(150) NOT NULL,
  `id_cms_privileges` int(11) NOT NULL,
  `id_cms_companies` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `cms_users` (`id`, `created_at`, `updated_at`, `name`, `photo`, `email`, `password`, `id_cms_privileges`, `id_cms_companies`) VALUES(13, '2016-08-06 15:11:36', '0000-00-00 00:00:00', 'Administrator', '2016-08/159643077b72aadfeb8a6eaa69c347b6.jpg', 'admin@crudbooster.com', '$2y$10$pSvsrFjckNvWvvY0PRewvujejhFLED1hYlPJGRYmhGmGs6ZNx/Voy', 3, 1);


ALTER TABLE `cms_apicustom`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permalink` (`permalink`);

ALTER TABLE `cms_companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `cms_dashboard`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cms_filemanager`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_md5` (`id_md5`);

ALTER TABLE `cms_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipaddress` (`ipaddress`),
  ADD KEY `useragent` (`useragent`),
  ADD KEY `url` (`url`),
  ADD KEY `id_cms_users` (`id_cms_users`);

ALTER TABLE `cms_menus`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cms_menus_groups`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cms_moduls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `path` (`path`),
  ADD KEY `id_cms_moduls_group` (`id_cms_moduls_group`);

ALTER TABLE `cms_moduls_group`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_group` (`nama_group`);

ALTER TABLE `cms_pages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cms_posts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cms_posts_categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cms_privileges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `cms_privileges_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_privileges` (`id_cms_privileges`,`id_cms_moduls`),
  ADD UNIQUE KEY `id_cms_privileges` (`id_cms_privileges`,`id_cms_moduls`),
  ADD KEY `id_cms_moduls` (`id_cms_moduls`);

ALTER TABLE `cms_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `cms_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);


ALTER TABLE `cms_apicustom`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cms_companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `cms_dashboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `cms_filemanager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cms_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
ALTER TABLE `cms_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `cms_menus_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `cms_moduls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;
ALTER TABLE `cms_moduls_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `cms_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `cms_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `cms_posts_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `cms_privileges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `cms_privileges_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=884;
ALTER TABLE `cms_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
ALTER TABLE `cms_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
