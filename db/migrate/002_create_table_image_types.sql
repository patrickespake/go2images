CREATE TABLE image_types
(
  id int(11) NOT NULL auto_increment,
  name varchar(150) NOT NULL,
  width decimal(10,2),
  height decimal(10,2),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
