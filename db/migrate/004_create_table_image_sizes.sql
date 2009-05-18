CREATE TABLE image_sizes
(
  id INT(11) NOT NULL auto_increment,
  image_id INT(11) NOT NULL,
  image_type_id INT(11) NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_content_type VARCHAR(255) NOT NULL,
  file_size INT(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE image_sizes ADD CONSTRAINT fk_image_size_image FOREIGN KEY (image_id) REFERENCES images (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE image_sizes ADD CONSTRAINT fk_image_size_image_type FOREIGN KEY (image_type_id) REFERENCES image_types (id) ON DELETE CASCADE ON UPDATE CASCADE;
