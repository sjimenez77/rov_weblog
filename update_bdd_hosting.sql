## 27/09/2014 13:58
CREATE TABLE Region (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE Wine (id INT AUTO_INCREMENT NOT NULL, winery_id INT DEFAULT NULL, brand VARCHAR(255) NOT NULL, type VARCHAR(3) NOT NULL, varieties VARCHAR(255) DEFAULT NULL, points SMALLINT NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, year SMALLINT NOT NULL, published TINYINT(1) NOT NULL, INDEX IDX_F63ECB5632FAE8E8 (winery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE Winery (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, postal VARCHAR(15) DEFAULT NULL, phone VARCHAR(15) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, web VARCHAR(255) DEFAULT NULL, INDEX IDX_955ED7C798260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE Wine ADD CONSTRAINT FK_F63ECB5632FAE8E8 FOREIGN KEY (winery_id) REFERENCES Winery (id) ON DELETE CASCADE;
ALTER TABLE Winery ADD CONSTRAINT FK_955ED7C798260155 FOREIGN KEY (region_id) REFERENCES Region (id) ON DELETE CASCADE;

## 27/09/2014 19:05 
ALTER TABLE Wine ADD created DATETIME NOT NULL, ADD updated DATETIME NOT NULL;

## 27/09/2014 19:34
ALTER TABLE Wine ADD image VARCHAR(255) DEFAULT NULL;

## 27/09/2014 20:42
ALTER TABLE Wine ADD image_name VARCHAR(255) NOT NULL, DROP image;

## 11/10/2014 13:48
ALTER TABLE Region ADD slug VARCHAR(255) NOT NULL;
ALTER TABLE Winery ADD slug VARCHAR(255) NOT NULL;