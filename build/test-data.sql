INSERT INTO `carousel` (`id`, `label`)
VALUES (1, 'carousel 1'), (2, 'carousel 2'), (3, 'carousel 3');

INSERT INTO `frame` (`id`, `carousel_id`, `url`, `duration`)
VALUES (1, 1, 'https://sewanee.environmentaldashboard.org/calendar/', 4000), (2, 2, 'https://obp.environmentaldashboard.org/calendar/', 4000), (3, 3, 'https://environmentaldashboard.org', 4000), (4, 1, 'https://environmentaldashboard.org/cleveland/chart/?meter0=2813', 4000), (5, 1, 'https://environmentaldashboard.org/community-voices/public/digital-signage.php', 4000), (6, 2, 'https://oberlin.environmentaldashboard.org/calendar/', 4000);

INSERT INTO `display` (`id`, `label`)
VALUES (1, 'display 1');

INSERT INTO `presentation` (`id`, `template_id`, `display_id`, `label`, `skip`, `duration`)
VALUES (1, 2, 1, 'marquee pres', 0, 25000), (2, 1, 1, 'fullscreen pres', 0, 25000);

INSERT INTO `carousel_presentation_map` (`id`, `presentation_id`, `carousel_id`, `template_key`)
VALUES (1, 2, 1, 'url1'), (2, 1, 3, 'url1'), (3, 1, 2, 'url2');

INSERT INTO `remote_controller` (`id`, `label`, `template_id`)
VALUES (1, "test controller", 0);

INSERT INTO `button` (`id`, `controller_id`, `trigger_frame_id`, `on_display_id`, `twig_key`)
VALUES (1, 1, 5, 1, "btn1"), (2, 1, 4, 1, "btn2");