CREATE TABLE categories (
                            id CHAR(36) PRIMARY KEY,
                            name VARCHAR(255) NOT NULL,
                            parent_id CHAR(36) NULL,
                            FOREIGN KEY (parent_id) REFERENCES categories(id)
);

CREATE TABLE courses (
                         id CHAR(36) PRIMARY KEY,
                         name VARCHAR(255) NOT NULL,
                         description TEXT,
                         image_preview VARCHAR(255),  -- this is a missing field I forgot to add
                         category_id CHAR(36),
                         FOREIGN KEY (category_id) REFERENCES categories(id)
);