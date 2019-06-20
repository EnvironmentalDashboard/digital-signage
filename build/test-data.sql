--
-- Database: `digital_signage`
--

--
-- Dumping data for table `carousel`
--

INSERT INTO `carousel` (`id`, `label`) VALUES
(1, 'water use'),
(4, 'water use marquis'),
(5, 'water use2'),
(6, 'Water use time series'),
(7, 'Electricity 1'),
(8, 'Electricity2'),
(9, 'Electricity time series'),
(10, 'Heat map'),
(11, 'Electricity Marquis 3 (heat maps)'),
(14, 'Electricity Marquis 1'),
(15, 'Natural Gas time series'),
(16, 'Electricity Marquis 2 (elec ts)');

--
-- Dumping data for table `carousel_presentation_map`
--

INSERT INTO `carousel_presentation_map` (`id`, `presentation_id`, `carousel_id`, `template_key`) VALUES
(1, 1, 1, 'url1'),
(2, 2, 6, 'url1'),
(3, 2, 4, 'url2'),
(4, 3, 5, 'url1'),
(5, 1, 1, 'url1'),
(6, 2, 6, 'url1'),
(7, 2, 4, 'url2'),
(8, 3, 5, 'url1'),
(9, 1, 1, 'url1'),
(10, 2, 6, 'url1'),
(11, 2, 4, 'url2'),
(12, 3, 5, 'url1'),
(13, 4, 7, 'url1'),
(14, 5, 9, 'url1'),
(15, 5, 14, 'url2'),
(16, 6, 8, 'url1'),
(17, 1, 1, 'url1'),
(18, 2, 6, 'url1'),
(19, 2, 4, 'url2'),
(20, 3, 5, 'url1'),
(21, 4, 7, 'url1'),
(22, 5, 9, 'url1'),
(23, 5, 16, 'url2'),
(24, 6, 8, 'url1'),
(25, 7, 15, 'url1'),
(26, 7, 14, 'url2'),
(27, 1, 1, 'url1'),
(28, 2, 6, 'url1'),
(29, 2, 4, 'url2'),
(30, 3, 5, 'url1'),
(31, 4, 7, 'url1'),
(32, 5, 9, 'url1'),
(33, 5, 16, 'url2'),
(34, 6, 8, 'url1'),
(35, 7, 15, 'url1'),
(36, 7, 14, 'url2'),
(37, 1, 1, 'url1'),
(38, 2, 6, 'url1'),
(39, 2, 4, 'url2'),
(40, 3, 5, 'url1'),
(41, 4, 7, 'url1'),
(42, 5, 9, 'url1'),
(43, 5, 16, 'url2'),
(44, 6, 8, 'url1'),
(45, 7, 15, 'url1'),
(46, 7, 14, 'url2'),
(47, 8, 10, 'url1'),
(48, 8, 11, 'url2'),
(49, 1, 1, 'url1'),
(50, 2, 6, 'url1'),
(51, 2, 4, 'url2'),
(52, 3, 5, 'url1'),
(53, 4, 7, 'url1'),
(54, 5, 9, 'url1'),
(55, 5, 16, 'url2'),
(56, 6, 8, 'url1'),
(57, 7, 15, 'url1'),
(58, 7, 14, 'url2'),
(59, 8, 10, 'url1'),
(60, 8, 11, 'url2'),
(61, 1, 1, 'url1'),
(62, 2, 6, 'url1'),
(63, 2, 4, 'url2'),
(64, 3, 5, 'url1'),
(65, 4, 7, 'url1'),
(66, 5, 9, 'url1'),
(67, 5, 16, 'url2'),
(68, 6, 8, 'url1'),
(69, 7, 15, 'url1'),
(70, 7, 14, 'url2'),
(71, 8, 10, 'url1'),
(72, 8, 11, 'url2');

--
-- Dumping data for table `display`
--

INSERT INTO `display` (`id`, `label`) VALUES
(1, 'Demo GLSC');

--
-- Dumping data for table `frame`
--

INSERT INTO `frame` (`id`, `carousel_id`, `url`, `duration`) VALUES
(1, 1, 'https://docs.google.com/presentation/d/1ZlCbIl9vDdiX5QZplQ8x6loV3raaaslBdm8KQUPZSzU/edit#slide=id.g5c14a9d155_0_0', 48000),
(2, 5, 'https://docs.google.com/presentation/d/1b1xJWd2NJ7rt66PUOnSN3R8PxSZqFkcENs4LtDIfgyY/edit#slide=id.g5c150784e6_0_0', 91000),
(3, 6, 'https://cleveland.environmentaldashboard.org/cleveland/chart/index.php?meter0=2941&hide_menu=1&hide_navbar=1', 30000),
(4, 7, 'https://docs.google.com/presentation/d/1OB_ieQiCQMw75kNCP2nZumd6Z2Mg0O-zNepFg-pRd4Y/edit#slide=id.p', 7000),
(5, 8, 'https://docs.google.com/presentation/d/1iee-N1NtF9FBpMvsSU6hQpLFsCaIt4hiXSzwApMveww/edit#slide=id.g5c1476f472_0_25', 84000),
(6, 9, 'https://cleveland.environmentaldashboard.org/cleveland/chart/index.php?meter0=2813&hide_menu=1&hide_navbar=1', 30000),
(7, 10, 'https://palmer.buildingos.com/reports/report/c0f3605cff0911e8af4202420aff085e', 46000),
(8, 11, 'https://docs.google.com/presentation/d/1s-yppZhsY9isBhpvc4l5Ir0W9QY_09zhLw7Cf0Y3I10/edit?usp=sharing', 46000),
(9, 14, 'https://docs.google.com/presentation/d/1-59xaViol1W7F26d46iPQ2PSMQN9tHnxHUpaLoF8GuA/edit#slide=id.p', 30000),
(10, 15, 'https://cleveland.environmentaldashboard.org/cleveland/chart/index.php?meter0=2942&hide_menu=1&time_frame=week&hide_navbar=1', 30000),
(11, 16, 'https://docs.google.com/presentation/d/1hEAt9dN-1RfqeqBoXBXIJ_CBBX3rm0Z9WPT0bhKWWwE/edit#slide=id.p', 30000),
(12, 4, 'https://docs.google.com/presentation/d/13LlFML-UKbHzoSCoOR4aV6NikMoLg8KGCf5z0wldI_w/edit#slide=id.p', 30000);

--
-- Dumping data for table `google_slides`
--

INSERT INTO `google_slides` (`id`, `presentation_id`, `data`) VALUES
(1, '1ZlCbIl9vDdiX5QZplQ8x6loV3raaaslBdm8KQUPZSzU', '[10000, 7000, 7000, 7000, 10000, 7000]'),
(3, '1b1xJWd2NJ7rt66PUOnSN3R8PxSZqFkcENs4LtDIfgyY', '[10000, 7000, 13000, 7000, 12000, 7000, 7000, 7000, 7000, 7000, 7000]'),
(4, '1OB_ieQiCQMw75kNCP2nZumd6Z2Mg0O-zNepFg-pRd4Y', '[7000]'),
(5, '1iee-N1NtF9FBpMvsSU6hQpLFsCaIt4hiXSzwApMveww', '[15000, 10000, 7000, 7000, 7000, 7000, 10000, 7000, 14000]'),
(6, '1s-yppZhsY9isBhpvc4l5Ir0W9QY_09zhLw7Cf0Y3I10', '[7000, 7000, 5000, 5000, 5000, 5000, 5000, 7000]'),
(7, '1-59xaViol1W7F26d46iPQ2PSMQN9tHnxHUpaLoF8GuA', '[10000, 10000, 10000]'),
(8, '1hEAt9dN-1RfqeqBoXBXIJ_CBBX3rm0Z9WPT0bhKWWwE', '[10000, 10000, 10000]'),
(9, '13LlFML-UKbHzoSCoOR4aV6NikMoLg8KGCf5z0wldI_w', '[10000, 10000, 10000]');

--
-- Dumping data for table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20190620160652', '2019-06-20 16:06:53');

--
-- Dumping data for table `presentation`
--

INSERT INTO `presentation` (`id`, `template_id`, `display_id`, `label`, `skip`, `duration`) VALUES
(1, 1, 1, 'Presentation for display #1', 0, 48000),
(2, 2, 1, 'Presentation for display #1', 0, 30000),
(3, 3, 1, 'Presentation for display #1', 0, 91000),
(4, 4, 1, 'Presentation for display #1', 0, 7000),
(5, 5, 1, 'Presentation for display #1', 0, 30000),
(6, 6, 1, 'Presentation for display #1', 0, 84000),
(7, 7, 1, 'Presentation for display #1', 0, 30000),
(8, 8, 1, 'Presentation for display #1', 0, 48000);

--
-- Dumping data for table `remote_controller`
--

INSERT INTO `remote_controller` (`id`, `template_id`, `label`) VALUES
(1, -3, 'john demo');

--
-- Dumping data for table `template`
--

INSERT INTO `template` (`id`, `twig`) VALUES
(-6, '<div class="button" data-twig="btn1" style="height:50%;width:25%;">{{ btn1|raw }}</div><div class="button" data-twig="btn2" style="height:50%;width:25%;">{{ btn2|raw }}</div><div class="button" data-twig="btn3" style="height:50%;width:25%;">{{ btn3|raw }}</div><div class="button" data-twig="btn4" style="height:50%;width:25%;">{{ btn4|raw }}</div><div class="button" data-twig="btn5" style="height:50%;width:25%;">{{ btn5|raw }}</div><div class="button" data-twig="btn6" style="height:50%;width:25%;">{{ btn6|raw }}</div><div class="button" data-twig="btn7" style="height:50%;width:25%;">{{ btn7|raw }}</div><div class="button" data-twig="btn8" style="height:50%;width:25%;">{{ btn8|raw }}</div>'),
(-5, '<div class="button" data-twig="btn1" style="height:33.333%;width:50%;">{{ btn1|raw }}</div><div class="button" data-twig="btn2" style="height:33.333%;width:50%;">{{ btn2|raw }}</div><div class="button" data-twig="btn3" style="height:33.333%;width:50%;">{{ btn3|raw }}</div><div class="button" data-twig="btn4" style="height:33.333%;width:50%;">{{ btn4|raw }}</div><div class="button" data-twig="btn5" style="height:33.333%;width:50%;">{{ btn5|raw }}</div><div class="button" data-twig="btn6" style="height:33.333%;width:50%;">{{ btn6|raw }}</div>'),
(-4, '<div class="button" data-twig="btn1" style="height:50%;width:50%;">{{ btn1|raw }}</div><div class="button" data-twig="btn2" style="height:50%;width:50%;">{{ btn2|raw }}</div><div class="button" data-twig="btn3" style="height:50%;width:50%;">{{ btn3|raw }}</div><div class="button" data-twig="btn4" style="height:50%;width:50%;">{{ btn4|raw }}</div>'),
(-3, '<div class="button" data-twig="btn1" style="height:100%;width:50%;">{{ btn1|raw }}</div><div class="button" data-twig="btn2" style="height:100%;width:50%;">{{ btn2|raw }}</div>'),
(-2, '<div class="carousel" data-twig="url1" style="height:80%;width:100%;position:relative">{{ url1|raw }}</div><div class="carousel" data-twig="url2" style="height:20%;width:100%;position:relative">{{ url2|raw }}</div> '),
(-1, '<div class="carousel" data-twig="url1" style="height:100%;width:100%;position:relative">{{ url1|raw }}</div> '),
(1, '<div class="carousel" data-twig="url1" style="height:100%;width:100%;position:relative">{{ url1|raw }}</div> '),
(2, '<div class="carousel" data-twig="url1" style="height:80%;width:100%;position:relative">{{ url1|raw }}</div><div class="carousel" data-twig="url2" style="height:20%;width:100%;position:relative">{{ url2|raw }}</div> '),
(3, '<div class="carousel" data-twig="url1" style="height:100%;width:100%;position:relative">{{ url1|raw }}</div> '),
(4, '<div class="carousel" data-twig="url1" style="height:100%;width:100%;position:relative">{{ url1|raw }}</div> '),
(5, '<div class="carousel" data-twig="url1" style="height:80%;width:100%;position:relative">{{ url1|raw }}</div><div class="carousel" data-twig="url2" style="height:20%;width:100%;position:relative">{{ url2|raw }}</div> '),
(6, '<div class="carousel" data-twig="url1" style="height:100%;width:100%;position:relative">{{ url1|raw }}</div> '),
(7, '<div class="carousel" data-twig="url1" style="height:80%;width:100%;position:relative">{{ url1|raw }}</div><div class="carousel" data-twig="url2" style="height:20%;width:100%;position:relative">{{ url2|raw }}</div> '),
(8, '<div class="carousel" data-twig="url1" style="height:80%;width:100%;position:relative">{{ url1|raw }}</div><div class="carousel" data-twig="url2" style="height:20%;width:100%;position:relative">{{ url2|raw }}</div> ');
