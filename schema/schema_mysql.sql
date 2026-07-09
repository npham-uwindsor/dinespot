-- Uncomment the following lines to drop the existing database and create the new one
-- SET FOREIGN_KEY_CHECKS = 0;
-- DROP DATABASE IF EXISTS dinespot;
-- CREATE DATABASE dinespot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE dinespot;

-- ============================================================
-- TABLES
-- ============================================================

DROP TABLE IF EXISTS favourites;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS site_settings;
DROP TABLE IF EXISTS restaurants;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    role ENUM('client', 'admin') NOT NULL DEFAULT 'client',
    status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
    phone VARCHAR(30) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    cuisine VARCHAR(80) NOT NULL,
    city VARCHAR(80) NOT NULL,
    province VARCHAR(10) NOT NULL,
    description TEXT NOT NULL,
    address VARCHAR(255) NULL,
    image_path VARCHAR(255) NULL,
    latitude DECIMAL(10, 7) NULL,
    longitude DECIMAL(10, 7) NULL,
    price_range TINYINT NOT NULL DEFAULT 2,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_restaurants_price_range CHECK (price_range BETWEEN 1 AND 4)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    category VARCHAR(80) NOT NULL,
    name VARCHAR(120) NOT NULL,
    description TEXT NULL,
    price DECIMAL(8, 2) NOT NULL,
    CONSTRAINT fk_menu_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5),
    CONSTRAINT uq_review_user_restaurant UNIQUE (restaurant_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    user_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    party_size TINYINT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reservations_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    CONSTRAINT fk_reservations_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_reservations_party_size CHECK (party_size BETWEEN 1 AND 20),
    CONSTRAINT chk_reservations_status CHECK (status IN ('pending', 'approved', 'rejected', 'cancelled', 'completed'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE favourites (
    user_id INT NOT NULL,
    restaurant_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, restaurant_id),
    CONSTRAINT fk_favourites_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_favourites_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE site_settings (
    setting_key VARCHAR(80) PRIMARY KEY,
    setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_restaurants_cuisine ON restaurants(cuisine);
CREATE INDEX idx_restaurants_city ON restaurants(city);
CREATE FULLTEXT INDEX idx_restaurants_search ON restaurants(name, cuisine, description);
CREATE INDEX idx_reviews_restaurant ON reviews(restaurant_id);
CREATE INDEX idx_reservations_user ON reservations(user_id);
CREATE INDEX idx_reservations_status ON reservations(status);
CREATE INDEX idx_menu_items_restaurant ON menu_items(restaurant_id);

-- ============================================================
-- USERS
-- Passwords: admin123 (admin), client123 (all clients)
-- ============================================================

INSERT INTO users (email, password_hash, full_name, role, status, phone) VALUES
('admin@dinespot.ca', '$2y$10$bzv0krxBzwlWnQmeUHJ12uvarOSM5wHElAFwE8a710jI431gWjKIy', 'Site Administrator', 'admin', 'active', '5195550100'),
('sarah.chen@example.com', '$2y$10$yK8ZkiIX.i.A.RfIueKpzuygEfPvPaVkxCKu4q6TQPCqVP0nHKZnq', 'Sarah Chen', 'client', 'active', '4165550111'),
('marcus.roy@example.com', '$2y$10$yK8ZkiIX.i.A.RfIueKpzuygEfPvPaVkxCKu4q6TQPCqVP0nHKZnq', 'Marcus Roy', 'client', 'active', '5145550222'),
('amira.haddad@example.com', '$2y$10$yK8ZkiIX.i.A.RfIueKpzuygEfPvPaVkxCKu4q6TQPCqVP0nHKZnq', 'Amira Haddad', 'client', 'active', '6045550333'),
('jordan.lee@example.com', '$2y$10$yK8ZkiIX.i.A.RfIueKpzuygEfPvPaVkxCKu4q6TQPCqVP0nHKZnq', 'Jordan Lee', 'client', 'suspended', '4035550444');

-- ============================================================
-- RESTAURANTS (20 records)
-- ============================================================

INSERT INTO restaurants (name, cuisine, city, province, description, address, image_path, latitude, longitude, price_range, is_active) VALUES
('Au Pied de Cochon', 'French', 'Montreal', 'QC', 'Experience decadent, foie gras-heavy Quebecois-French comfort food.', '536 Avenue Duluth E, Montreal, QC', 'assets/images/restaurants/french.jpg', 45.5271000, -73.5744000, 4, 1),
('Sushi Masaki Saito', 'Japanese', 'Toronto', 'ON', 'A highly celebrated 2-Michelin-starred Edomae sushi experience.', '88 Avenue Rd, Toronto, ON', 'assets/images/restaurants/japan.jpg', 43.6711000, -79.3956000, 4, 1),
('PAI', 'Thai', 'Toronto', 'ON', 'Authentic Northern Thai street-style food and hospitality.', '18 Duncan St, Toronto, ON', 'assets/images/restaurants/thai.jpg', 43.6475000, -79.3892000, 2, 1),
('Restaurant Pearl Morissette', 'Canadian', 'Jordan Station', 'ON', 'Farm-to-table dining featuring French-inspired Canadian dishes.', '3953 Jordan Rd, Jordan Station, ON', 'assets/images/restaurants/canada.jpg', 43.1550000, -79.3670000, 4, 1),
('Buca', 'Italian', 'Toronto', 'ON', 'Known for rustic Italian cooking, cured meats, and authentic seafood.', '604 King St W, Toronto, ON', 'assets/images/restaurants/italy.jpg', 43.6443000, -79.4011000, 3, 1),
('Vij''s', 'Modern Indian', 'Vancouver', 'BC', 'Award-winning, inventive, and globally acclaimed Indian cuisine.', '3106 Main St, Vancouver, BC', 'assets/images/restaurants/india.jpg', 49.2557000, -123.1006000, 3, 1),
('Dasha', 'Chinese', 'Toronto', 'ON', 'High-end contemporary Pan-Asian and Northern Chinese cuisine.', '1229 Dundas St W, Toronto, ON', 'assets/images/restaurants/china.jpg', 43.6492000, -79.4201000, 3, 1),
('La Carnita', 'Mexican', 'Toronto', 'ON', 'Vibrant street-style tacos and Mexican street corn.', '106 John St, Toronto, ON', 'assets/images/restaurants/mexico.jpg', 43.6470000, -79.3925000, 2, 1),
('Pho 99', 'Vietnamese', 'Toronto', 'ON', 'Praised for authentic, hearty, and comforting bowls of pho.', '1263 Wilson Ave, Toronto, ON', 'assets/images/restaurants/viet.jpg', 43.7255000, -79.4712000, 1, 1),
('Messob', 'Ethiopian', 'Toronto', 'ON', 'Traditional family-style Ethiopian dishes served on injera bread.', '265 Danforth Ave, Toronto, ON', 'assets/images/restaurants/ethiopian.jpg', 43.6780000, -79.3515000, 2, 1),
('Milos', 'Greek', 'Montreal', 'QC', 'World-class Mediterranean seafood and whole grilled fish.', '5357 Avenue du Parc, Montreal, QC', 'assets/images/restaurants/greek.jpg', 45.5234000, -73.5998000, 4, 1),
('Bar Raval', 'Spanish', 'Toronto', 'ON', 'A beautifully designed Gaudi-inspired tapas bar.', '505 College St, Toronto, ON', 'assets/images/restaurants/spain.jpg', 43.6562000, -79.4098000, 3, 1),
('JinBar', 'Korean', 'Calgary', 'AB', 'Comfort food with fusion Korean-Canadian flavors like bulgogi pizza.', '1210 1 St SW, Calgary, AB', 'assets/images/restaurants/korea.jpg', 51.0428000, -114.0719000, 2, 1),
('Baviloca', 'Brazilian', 'Toronto', 'ON', 'Authentic Brazilian churrascaria and traditional fare.', '883 Queen St W, Toronto, ON', 'assets/images/restaurants/brazil.jpg', 43.6448000, -79.4105000, 3, 1),
('Allwyn''s Bakery', 'Caribbean', 'Toronto', 'ON', 'Famous for rich, flavorful jerk chicken and Jamaican patties.', '1610 Eglinton Ave W, Toronto, ON', 'assets/images/restaurants/carribean.jpg', 43.6978000, -79.4432000, 1, 1),
('Boustan', 'Lebanese', 'Montreal', 'QC', 'A legendary late-night spot for classic shawarma and falafel.', '2020 Crescent St, Montreal, QC', 'assets/images/restaurants/lebanon.jpg', 45.4979000, -73.5794000, 1, 1),
('Kay Pacha', 'Peruvian', 'Toronto', 'ON', 'Upscale, modern take on traditional Peruvian ceviches and anticuchos.', '74 Ossington Ave, Toronto, ON', 'assets/images/restaurants/peru.jpg', 43.6479000, -79.4197000, 3, 1),
('The Old Spaghetti Factory', 'German', 'Toronto', 'ON', 'A Canadian staple serving comforting European and Italian-style pasta.', '1 Austin Terrace, Toronto, ON', 'assets/images/restaurants/germany.jpg', 43.6780000, -79.4094000, 2, 1),
('Asado', 'Argentine', 'Vancouver', 'BC', 'Traditional Argentine parrillada and wood-fired meats.', '1319 Commercial Dr, Vancouver, BC', 'assets/images/restaurants/argentina.jpg', 49.2734000, -123.0695000, 3, 1),
('Big Bone BBQ', 'American', 'Windsor', 'ON', 'Southern-style smoked ribs, brisket, and pulled pork.', '2450 Dougall Ave, Windsor, ON', 'assets/images/restaurants/america.jpg', 42.2956000, -83.0066000, 2, 1);

-- ============================================================
-- MENU ITEMS (3 per restaurant => 60 records)
-- ============================================================

INSERT INTO menu_items (restaurant_id, category, name, description, price) VALUES
(1, 'Starters', 'Foie Gras Torchon', 'Quebecois classic with brioche and apple compote.', 24.00),
(1, 'Mains', 'Poutine au Foie Gras', 'Signature rich poutine with foie gras.', 38.00),
(1, 'Desserts', 'Sugar Pie', 'Traditional Quebec dessert with maple cream.', 12.00),
(2, 'Starters', 'Edomae Otsumami', 'Seasonal Japanese small plates.', 28.00),
(2, 'Mains', 'Omakase Nigiri Set', 'Chef-selected sushi tasting.', 120.00),
(3, 'Starters', 'Khao Soi', 'Northern Thai coconut curry noodle soup.', 18.00),
(3, 'Mains', 'Pad Thai', 'Stir-fried rice noodles with tamarind and peanuts.', 19.00),
(3, 'Desserts', 'Mango Sticky Rice', 'Sweet coconut rice with fresh mango.', 11.00),
(4, 'Starters', 'Farm Greens', 'Seasonal salad from local producers.', 16.00),
(4, 'Mains', 'Tasting Menu', 'Multi-course farm-to-table experience.', 95.00),
(4, 'Desserts', 'Poached Pear', 'House dessert with Ontario fruit.', 13.00),
(5, 'Starters', 'Charcuterie Board', 'Cured meats, pickles, and grilled bread.', 22.00),
(5, 'Mains', 'Buca Bucatini', 'Spicy tomato pasta with pancetta.', 26.00),
(5, 'Desserts', 'Tiramisu', 'Classic Italian coffee dessert.', 12.00),
(6, 'Starters', 'Papadum Trio', 'Crisp lentil wafers with chutneys.', 8.00),
(6, 'Mains', 'Lamb Popsicles', 'Award-winning fenugreek-cream lamb.', 32.00),
(6, 'Desserts', 'Kulfi', 'Traditional Indian ice cream.', 10.00),
(7, 'Starters', 'Scallion Pancake', 'Crispy Northern Chinese pancake.', 12.00),
(7, 'Mains', 'Peking Duck', 'Whole roasted duck with pancakes.', 68.00),
(7, 'Desserts', 'Sesame Balls', 'Fried glutinous rice with red bean.', 9.00),
(8, 'Starters', 'Elote', 'Mexican street corn with cotija.', 9.00),
(8, 'Mains', 'Al Pastor Tacos', 'Marinated pork tacos with pineapple.', 16.00),
(8, 'Desserts', 'Churros', 'Cinnamon sugar with chocolate dip.', 10.00),
(9, 'Starters', 'Spring Rolls', 'Crispy vegetable rolls with nuoc cham.', 7.00),
(9, 'Mains', 'Rare Beef Pho', 'Hearty pho with fresh herbs.', 15.00),
(9, 'Desserts', 'Coconut Coffee', 'Vietnamese iced coffee.', 6.00),
(10, 'Starters', 'Sambusa', 'Spiced lentil pastries.', 8.00),
(10, 'Mains', 'Doro Wat Platter', 'Chicken stew with injera and sides.', 22.00),
(11, 'Starters', 'Grilled Octopus', 'Mediterranean octopus with lemon.', 26.00),
(11, 'Mains', 'Whole Grilled Lavraki', 'Sea bass prepared tableside.', 58.00),
(11, 'Desserts', 'Greek Yogurt', 'With thyme honey and walnuts.', 11.00),
(12, 'Starters', 'Pan con Tomate', 'Toasted bread with tomato and olive oil.', 9.00),
(12, 'Mains', 'Patatas Bravas', 'Crispy potatoes with spicy sauce.', 12.00),
(12, 'Desserts', 'Crema Catalana', 'Spanish custard with caramel top.', 10.00),
(13, 'Starters', 'Kimchi Fries', 'Fries topped with kimchi and sauce.', 11.00),
(13, 'Mains', 'Bulgogi Pizza', 'Korean-Canadian fusion favourite.', 19.00),
(13, 'Desserts', 'Bingsu', 'Shaved ice dessert with toppings.', 12.00),
(14, 'Mains', 'Churrasco Platter', 'Assorted grilled meats.', 42.00),
(14, 'Desserts', 'Brigadeiro', 'Chocolate truffle dessert.', 8.00),
(15, 'Starters', 'Jamaican Patty', 'Spiced beef or vegetable patty.', 4.50),
(16, 'Starters', 'Shawarma', 'Lebanese street-style sandwich.', 12.00),
(16, 'Mains', 'Shawarma Platter', 'Assorted grilled meats.', 42.00),
(16, 'Desserts', 'Tahini Halva', 'Lebanese dessert with honey.', 10.00),
(17, 'Starters', 'Tzatziki', 'Greek yogurt with cucumber.', 8.00),
(17, 'Mains', 'Moussaka', 'Greek baked eggplant with meat.', 22.00),
(17, 'Desserts', 'Loukoumades', 'Greek honeycomb with cinnamon.', 9.00),
(18, 'Starters', 'Ceviche', 'Peruvian seafood with lime.', 14.00),
(18, 'Mains', 'Anticuchos', 'Peruvian skewers with spicy sauce.', 20.00),
(18, 'Desserts', 'Pisco Sour', 'Peruvian sour cocktail.', 11.00),
(19, 'Starters', 'Pierogi', 'Polish dumplings with sweet cheese.', 8.00),
(19, 'Mains', 'Golabki', 'Beef-filled cabbage rolls.', 18.00),
(19, 'Desserts', 'Panna Cotta', 'Italian cream cheese dessert.', 10.00),
(20, 'Starters', 'Pulled Pork', 'Smoked pork shoulder with BBQ sauce.', 14.00),
(20, 'Mains', 'Smoked Ribs', 'Slow-smoked pork ribs with coleslaw.', 22.00),
(20, 'Desserts', 'Apple Pie', 'Classic American dessert.', 8.00);

-- ============================================================
-- REVIEWS (11 records)
-- ============================================================

INSERT INTO reviews (restaurant_id, user_id, rating, comment) VALUES
(1, 2, 5, 'Rich, unforgettable flavours. A must-visit in Montreal.'),
(1, 3, 4, 'Busy atmosphere but the foie gras dishes are incredible.'),
(2, 2, 5, 'Best sushi experience I have had in Toronto.'),
(3, 4, 5, 'Feels like Bangkok. The khao soi is outstanding.'),
(5, 2, 4, 'Excellent pasta and charcuterie board.'),
(6, 3, 5, 'Creative Indian dishes with incredible depth of flavour.'),
(8, 4, 4, 'Fun tacos and great cocktails.'),
(11, 2, 5, 'Fresh seafood and perfect grilled fish.'),
(13, 3, 4, 'Bulgogi pizza is surprisingly great.'),
(16, 4, 5, 'Best shawarma in Montreal after a night out.'),
(20, 2, 4, 'Smoky BBQ and generous portions. Great value.');

-- ============================================================
-- RESERVATIONS (6 records)
-- ============================================================

INSERT INTO reservations (restaurant_id, user_id, reservation_date, reservation_time, party_size, status, notes) VALUES
(2, 2, '2026-06-15', '19:00:00', 2, 'approved', 'Anniversary dinner'),
(3, 3, '2026-06-18', '18:30:00', 4, 'pending', 'Window seat if possible'),
(5, 4, '2026-06-20', '20:00:00', 3, 'approved', NULL),
(8, 2, '2026-06-22', '19:30:00', 2, 'pending', NULL),
(11, 3, '2026-06-25', '18:00:00', 5, 'rejected', 'Fully booked that evening'),
(20, 4, '2026-06-28', '17:30:00', 6, 'approved', 'Birthday celebration');

-- ============================================================
-- FAVOURITES (10 records)
-- ============================================================

INSERT INTO favourites (user_id, restaurant_id) VALUES
(2, 1), 
(2, 2), 
(2, 11), 
(2, 20),
(3, 3), 
(3, 6), 
(3, 13),
(4, 5), 
(4, 8), 
(4, 16);

-- ============================================================
-- SITE SETTINGS (2 records)
-- ============================================================

INSERT INTO site_settings (setting_key, setting_value) VALUES
('active_theme', 'classic'),
('site_version', '2.0.0');

SET FOREIGN_KEY_CHECKS = 1;
