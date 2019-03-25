INSERT INTO "frame" ("id","carousel_id","url","duration") VALUES (3,1,'https://codepen.io',60),
 (4,2,'https://css-tricks.com',60),
 (5,3,'https://www.theonion.com',60);
INSERT INTO "presentation" ("id","template_id","display_id","label","skip","duration") VALUES (3,2,1,'pres 1',0,60),
 (4,1,1,'pres 2',0,60);
INSERT INTO "carousel_presentation_map" ("id","presentation_id","carousel_id","template_key") VALUES (5,3,1,'url1'),
 (6,3,2,'url2'),
 (7,4,3,'url1');
INSERT INTO "carousel" ("id","label") VALUES (1,'carousel 1'),
 (2,'carousel 2'),
 (3,'carousel 3');
INSERT INTO "display" ("id","label") VALUES (1,'display 1');