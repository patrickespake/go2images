CREATE TABLE images
(
  id INT(11) NOT NULL auto_increment,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  file_name VARCHAR(255) NOT NULL,
  file_content_type VARCHAR(255) NOT NULL,
  file_size INT(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
