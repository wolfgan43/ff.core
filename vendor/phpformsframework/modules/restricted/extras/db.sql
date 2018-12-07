create table support_city
(
  ID int auto_increment
    primary key,
  ID_state int null,
  ID_region int null,
  ID_province int null,
  name varchar(255) null,
  smart_url varchar(255) null,
  istat_code varchar(5) null,
  chief_town int(1) null,
  cap varchar(5) null,
  coord_title varchar(255) null,
  coord_lat double null,
  coord_lng double null,
  coord_zoom int(2) null
)
  engine=InnoDB
;

create index `support_city.ID_state`
  on support_city (ID_state)
;

create index `support_city.ID_region`
  on support_city (ID_region)
;

create index `support_city.ID_province`
  on support_city (ID_province)
;

create index smart_url
  on support_city (smart_url)
;

create table support_currency
(
  ID int auto_increment
    primary key,
  name varchar(255) null,
  code varchar(3) null,
  symbol char null
)
  engine=InnoDB
;

create table support_province
(
  ID int auto_increment
    primary key,
  ID_state int null,
  ID_region int null,
  name varchar(255) null,
  smart_url varchar(255) null,
  sigla varchar(2) null,
  zone char null,
  coord_title varchar(255) null,
  coord_lat double null,
  coord_lng double null,
  coord_zoom varchar(255) null
)
  engine=InnoDB
;

create index `support_province.ID_state`
  on support_province (ID_state)
;

create index `support_province.ID_region`
  on support_province (ID_region)
;

create index smart_url
  on support_province (smart_url)
;

create index sigla
  on support_province (sigla)
;

alter table support_city
  add constraint `support_city.ID_province`
foreign key (ID_province) references support_province (ID)
;

create table support_region
(
  ID int auto_increment
    primary key,
  ID_state int null,
  name varchar(255) null,
  smart_url varchar(255) null,
  zone varchar(1) null,
  coord_title varchar(255) null,
  coord_lat double null,
  coord_lng double null,
  coord_zoom int(2) null
)
  engine=InnoDB
;

create index ID_state
  on support_region (ID_state)
;

create index smart_url
  on support_region (smart_url)
;

alter table support_city
  add constraint `support_city.ID_region`
foreign key (ID_region) references support_region (ID)
;

alter table support_province
  add constraint `support_province.ID_region`
foreign key (ID_region) references support_region (ID)
;

create table support_state
(
  ID int auto_increment
    primary key,
  ID_currency int null,
  ID_lang int null,
  ID_zone int null,
  name varchar(255) null,
  abbreviation varchar(5) null,
  coord_title varchar(255) null,
  coord_lat double null,
  coord_lng double null,
  coord_zoom int(2) null,
  vat_enable int(1) null,
  vat int(3) null,
  code varchar(3) null,
  prefix varchar(4) null,
  constraint `support_state.ID_currency`
  foreign key (ID_currency) references support_currency (ID)
)
  engine=InnoDB
;

create index ID_currency
  on support_state (ID_currency)
;

create index ID_lang
  on support_state (ID_lang)
;

create index ID_zone
  on support_state (ID_zone)
;

create index abbreviation
  on support_state (abbreviation)
;

alter table support_city
  add constraint `support_city.ID_state`
foreign key (ID_state) references support_state (ID)
;

alter table support_province
  add constraint `support_province.ID_state`
foreign key (ID_state) references support_state (ID)
;

alter table support_region
  add constraint `support_region.state`
foreign key (ID_state) references support_state (ID)
;


INSERT INTO support_currency (ID, name, code, symbol) VALUES (1, 'Euro', 'EUR', '&');
INSERT INTO support_currency (ID, name, code, symbol) VALUES (2, 'British pound', 'GBP', '&');

INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (1, null, null, 0, 'Abkhazia', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (2, null, null, 0, 'Afghanistan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (3, null, null, 0, 'Akrotiri and Dhekelia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (4, null, null, 0, 'Aland', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (5, null, null, 0, 'Albania', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (6, null, null, 0, 'Algeria', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (7, null, null, 0, 'American Samoa', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (8, null, null, 0, 'Andorra', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (9, null, null, 0, 'Angola', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (10, null, null, 0, 'Anguilla', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (11, null, null, 0, 'Antigua and Barbuda', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (12, null, null, 0, 'Argentina', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (13, null, null, 0, 'Armenia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (14, null, null, 5, 'Aruba', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (15, null, null, 0, 'Ascension Island', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (16, null, null, 0, 'Australia', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (17, null, null, 2, 'Austria', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (18, null, null, 0, 'Azerbaijan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (19, null, null, 0, 'Bahamas, The', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (20, null, null, 0, 'Bahrain', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (21, null, null, 0, 'Bangladesh', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (22, null, null, 0, 'Barbados', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (23, null, null, 0, 'Belarus', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (24, null, null, 3, 'Belgium', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (25, null, null, 0, 'Belize', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (26, null, null, 0, 'Benin', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (27, null, null, 0, 'Bermuda', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (28, null, null, 0, 'Bhutan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (29, null, null, 0, 'Bolivia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (30, null, null, 0, 'Bosnia and Herzegovina', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (31, null, null, 0, 'Botswana', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (32, null, null, 0, 'Brazil', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (33, null, null, 0, 'Brunei', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (34, null, null, 0, 'Bulgaria', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (35, null, null, 0, 'Burkina Faso', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (36, null, null, 0, 'Burundi', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (37, null, null, 0, 'Cambodia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (38, null, null, 0, 'Cameroon', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (39, null, null, 0, 'Canada', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (40, null, null, 0, 'Cape Verde', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (41, null, null, 0, 'Cayman Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (42, null, null, 0, 'Central African Republic', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (43, null, null, 0, 'Chad', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (44, null, null, 0, 'Chile', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (45, null, null, 0, 'China', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (46, null, null, 0, 'Christmas Island', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (47, null, null, 0, 'Cocos (Keeling) Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (48, null, null, 0, 'Colombia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (49, null, null, 0, 'Comoros', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (50, null, null, 0, 'Congo', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (51, null, null, 0, 'Cook Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (52, null, null, 0, 'Costa Rica', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (53, null, null, 0, 'Cote d''Ivoire', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (54, null, null, 0, 'Croatia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (55, null, null, 0, 'Cuba', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (56, null, null, 0, 'Cyprus', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (57, null, null, 4, 'Czech Republic', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (58, null, null, 4, 'Denmark', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (59, null, null, 0, 'Djibouti', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (60, null, null, 0, 'Dominica', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (61, null, null, 0, 'Dominican Republic', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (62, null, null, 0, 'Ecuador', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (63, null, null, 0, 'Egypt', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (64, null, null, 0, 'El Salvador', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (65, null, null, 0, 'Equatorial Guinea', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (66, null, null, 0, 'Eritrea', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (67, null, null, 0, 'Estonia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (68, null, null, 0, 'Ethiopia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (69, null, null, 0, 'Falkland Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (70, null, null, 0, 'Faroe Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (71, null, null, 0, 'Fiji', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (72, null, null, 4, 'Finland', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (73, null, null, 0, 'France', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (74, null, null, 0, 'French Polynesia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (75, null, null, 0, 'Gabon', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (76, null, null, 0, 'Gambia, The', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (77, null, null, 0, 'Georgia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (78, null, null, 2, 'Germany', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (79, null, null, 0, 'Ghana', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (80, null, null, 0, 'Gibraltar', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (81, null, null, 0, 'Greece', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (82, null, null, 0, 'Greenland', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (83, null, null, 0, 'Grenada', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (84, null, null, 0, 'Guam', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (85, null, null, 0, 'Guatemala', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (86, null, null, 0, 'Guernsey', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (87, null, null, 0, 'Guinea', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (88, null, null, 0, 'Guinea-Bissau', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (89, null, null, 0, 'Guyana', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (90, null, null, 0, 'Haiti', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (91, null, null, 0, 'Honduras', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (92, null, null, 0, 'Hong Kong', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (93, null, null, 4, 'Hungary', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (94, null, null, 0, 'Iceland', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (95, null, null, 0, 'India', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (96, null, null, 0, 'Indonesia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (97, null, null, 0, 'Iran', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (98, null, null, 0, 'Iraq', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (99, null, null, 4, 'Ireland', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (100, null, null, 0, 'Isle of Man', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (101, null, null, 0, 'Israel', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (102, null, null, 1, 'Italy', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (103, null, null, 0, 'Jamaica', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (104, null, null, 0, 'Japan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (105, null, null, 0, 'Jersey', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (106, null, null, 0, 'Jordan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (107, null, null, 0, 'Kazakhstan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (108, null, null, 0, 'Kenya', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (109, null, null, 0, 'Kiribati', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (110, null, null, 0, 'Korea', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (111, null, null, 0, 'Kosovo', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (112, null, null, 0, 'Kuwait', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (113, null, null, 0, 'Kyrgyzstan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (114, null, null, 0, 'Laos', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (115, null, null, 0, 'Latvia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (116, null, null, 0, 'Lebanon', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (117, null, null, 0, 'Lesotho', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (118, null, null, 0, 'Liberia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (119, null, null, 0, 'Libya', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (120, null, null, 4, 'Liechtenstein', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (121, null, null, 0, 'Lithuania', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (122, null, null, 3, 'Luxembourg', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (123, null, null, 0, 'Macao', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (124, null, null, 0, 'Macedonia, Republic of', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (125, null, null, 0, 'Madagascar', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (126, null, null, 0, 'Malawi', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (127, null, null, 0, 'Malaysia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (128, null, null, 0, 'Maldives', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (129, null, null, 0, 'Mali', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (130, null, null, 0, 'Malta', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (131, null, null, 0, 'Marshall Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (132, null, null, 0, 'Mauritania', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (133, null, null, 0, 'Mauritius', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (134, null, null, 0, 'Mayotte', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (135, null, null, 0, 'Mexico', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (136, null, null, 0, 'Micronesia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (137, null, null, 0, 'Moldova', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (138, null, null, 0, 'Monaco', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (139, null, null, 0, 'Mongolia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (140, null, null, 0, 'Montenegro', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (141, null, null, 0, 'Montserrat', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (142, null, null, 0, 'Morocco', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (143, null, null, 0, 'Mozambique', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (144, null, null, 0, 'Myanmar', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (145, null, null, 0, 'Nagorno-Karabakh', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (146, null, null, 0, 'Namibia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (147, null, null, 0, 'Nauru', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (148, null, null, 0, 'Nepal', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (149, null, null, 0, 'Netherlands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (150, null, null, 0, 'Netherlands Antilles', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (151, null, null, 0, 'New Caledonia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (152, null, null, 0, 'New Zealand', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (153, null, null, 0, 'Nicaragua', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (154, null, null, 0, 'Niger', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (155, null, null, 0, 'Nigeria', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (156, null, null, 0, 'Niue', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (157, null, null, 0, 'Norfolk Island', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (158, null, null, 0, 'Northern Cyprus', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (159, null, null, 0, 'Northern Mariana Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (160, null, null, 4, 'Norway', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (161, null, null, 0, 'Oman', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (162, null, null, 0, 'Pakistan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (163, null, null, 0, 'Palau', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (164, null, null, 0, 'Palestine', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (165, null, null, 0, 'Panama', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (166, null, null, 0, 'Papua New Guinea', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (167, null, null, 0, 'Paraguay', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (168, null, null, 0, 'Peru', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (169, null, null, 0, 'Philippines', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (170, null, null, 0, 'Pitcairn Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (171, null, null, 4, 'Poland', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (172, null, null, 4, 'Portugal', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (173, null, null, 0, 'Pridnestrovie', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (174, null, null, 0, 'Puerto Rico', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (175, null, null, 0, 'Qatar', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (176, null, null, 0, 'Romania', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (177, null, null, 0, 'Russia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (178, null, null, 0, 'Rwanda', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (179, null, null, 0, 'Saint Barthelemy', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (180, null, null, 0, 'Saint Helena', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (181, null, null, 0, 'Saint Kitts and Nevis', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (182, null, null, 0, 'Saint Lucia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (183, null, null, 0, 'Saint Martin', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (184, null, null, 0, 'Saint Pierre and Miquelon', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (185, null, null, 0, 'Saint Vincent and the Grenadines', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (186, null, null, 0, 'Samoa', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (187, null, null, 0, 'San Marino', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (188, null, null, 0, 'Sao Tomé and Príncipe', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (189, null, null, 0, 'Saudi Arabia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (190, null, null, 0, 'Senegal', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (191, null, null, 0, 'Serbia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (192, null, null, 0, 'Seychelles', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (193, null, null, 0, 'Sierra Leone', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (194, null, null, 0, 'Singapore', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (195, null, null, 0, 'Slovakia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (196, null, null, 0, 'Slovenia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (197, null, null, 0, 'Solomon Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (198, null, null, 0, 'Somalia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (199, null, null, 0, 'Somaliland', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (200, null, null, 0, 'South Africa', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (201, null, null, 0, 'South Ossetia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (202, null, null, 3, 'Spain', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (203, null, null, 0, 'Sri Lanka', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (204, null, null, 0, 'Sudan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (205, null, null, 0, 'Suriname', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (206, null, null, 0, 'Svalbard', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (207, null, null, 0, 'Swaziland', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (208, null, null, 4, 'Sweden', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (209, null, null, 3, 'Switzerland', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (210, null, null, 0, 'Syria', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (211, null, null, 0, 'Tajikistan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (212, null, null, 0, 'Tanzania', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (213, null, null, 0, 'Thailand', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (214, null, null, 0, 'Timor-Leste', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (215, null, null, 0, 'Togo', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (216, null, null, 0, 'Tokelau', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (217, null, null, 0, 'Tonga', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (218, null, null, 0, 'Trinidad and Tobago', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (219, null, null, 0, 'Tristan da Cunha', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (220, null, null, 0, 'Tunisia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (221, null, null, 0, 'Turkey', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (222, null, null, 0, 'Turkmenistan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (223, null, null, 0, 'Turks and Caicos Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (224, null, null, 0, 'Tuvalu', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (225, null, null, 0, 'Uganda', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (226, null, null, 0, 'Ukraine', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (227, null, null, 0, 'United Arab Emirates', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (228, null, null, 3, 'United Kingdom', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (229, null, null, 0, 'United States', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (230, null, null, 0, 'Uruguay', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (231, null, null, 0, 'Uzbekistan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (232, null, null, 0, 'Vanuatu', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (233, null, null, 0, 'Vatican City', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (234, null, null, 0, 'Venezuela', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (235, null, null, 0, 'Vietnam', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (236, null, null, 0, 'Virgin Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (237, null, null, 0, 'Wallis and Futuna', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (238, null, null, 0, 'Western Sahara', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (239, null, null, 0, 'Yemen', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (240, null, null, 0, 'Zambia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (241, null, null, 0, 'Zimbabwe', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (245, null, null, 3, 'Holland', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (263, null, null, 4, 'Croazia', '', '', 0, 0, 0, 1, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (264, null, null, 0, 'Aland Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (265, null, null, 0, 'Antarctica', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (266, null, null, 0, 'Bahamas', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (267, null, null, 0, 'Bouvet Island', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (268, null, null, 0, 'British Indian Ocean Territory', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (269, null, null, 0, 'Brunei Darussalam', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (270, null, null, 0, 'Serbia and Montenegro', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (271, null, null, 0, 'Virgin Islands, U.S.', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (272, null, null, 0, 'Vatican City State', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (273, null, null, 0, 'French Southern Territories', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (274, null, null, 0, 'South Georgia and the South Sandwich Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (275, null, null, 0, 'Svalbard and Jan Mayen Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (276, null, null, 0, 'Lao People’s Democratic Republic', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (277, null, null, 0, 'Macau', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (278, null, null, 0, 'Macedonia, the Former Yugoslav Republic of', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (279, null, null, 0, 'Korea, Republic of (South)', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (280, null, null, 0, 'Tanzania, United Republic of', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (281, null, null, 0, 'Heard Island and Mcdonald Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (282, null, null, 0, 'Palestinian Territory, Occupied', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (283, null, null, 0, 'Viet Nam', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (284, null, null, 0, 'Korea, Democratic People’s Republic of (North)', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (285, null, null, 0, 'Gambia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (286, null, null, 0, 'Congo, Democratic Republic of the', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (287, null, null, 0, 'Falkland Islands (Malvinas)', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (288, null, null, 0, 'Pitcairn', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (289, null, null, 0, 'Virgin Islands, British', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (290, null, null, 0, 'Iran, Islamic Republic of', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (291, null, null, 0, 'Wallis and Futuna Islands', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (292, null, null, 0, 'Yugoslavia', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (293, null, null, 0, 'Reunion', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (294, null, null, 0, 'Russian Federation', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (295, null, null, 0, 'Croatia (Hrvatska)', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (296, null, null, 0, 'Martinique', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (297, null, null, 0, 'Neutral Zone', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (298, null, null, 0, 'Libyan Arab Jamahiriya', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (299, null, null, 0, 'Taiwan', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (300, null, null, 0, 'French Guiana', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (301, null, null, 0, 'Guadeloupe', '', '', 0, 0, 0, 0, 0, '', null);
INSERT INTO support_state (ID, ID_currency, ID_lang, ID_zone, name, abbreviation, coord_title, coord_lat, coord_lng, coord_zoom, vat_enable, vat, code, prefix) VALUES (302, null, null, 0, 'Cipro', '', '', 0, 0, 0, 0, 0, '', null);

INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (1, 102, 'Piemonte', 'piemonte', 'N', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (2, 102, 'Valle d''Aosta', 'valle-d-aosta', 'N', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (3, 102, 'Lombardia', 'lombardia', 'N', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (4, 102, 'Trentino Alto Adige', 'trentino-alto-adige', 'N', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (5, 102, 'Veneto', 'veneto', 'N', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (6, 102, 'Friuli Venezia Giulia', 'friuli-venezia-giulia', 'N', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (7, 102, 'Liguria', 'liguria', 'N', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (8, 102, 'Emilia Romagna', 'emilia-romagna', 'N', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (9, 102, 'Toscana', 'toscana', 'C', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (10, 102, 'Umbria', 'umbria', 'C', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (11, 102, 'Marche', 'marche', 'C', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (12, 102, 'Lazio', 'lazio', 'C', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (13, 102, 'Abruzzo', 'abruzzo', 'C', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (14, 102, 'Molise', 'molise', 'S', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (15, 102, 'Campania', 'campania', 'S', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (16, 102, 'Puglia', 'puglia', 'S', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (17, 102, 'Basilicata', 'basilicata', 'S', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (18, 102, 'Calabria', 'calabria', 'S', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (19, 102, 'Sicilia', 'sicilia', 'I', '', 0, 0, 0);
INSERT INTO support_region (ID, ID_state, name, smart_url, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (20, 102, 'Sardegna', 'sardegna', 'I', '', 0, 0, 0);

INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (1, 102, 1, 'Torino', 'torino', 'TO', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (2, 102, 1, 'Vercelli', 'vercelli', 'VC', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (3, 102, 1, 'Novara', 'novara', 'NO', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (4, 102, 1, 'Cuneo', 'cuneo', 'CN', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (5, 102, 1, 'Asti', 'asti', 'AT', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (6, 102, 1, 'Alessandria', 'alessandria', 'AL', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (7, 102, 2, 'Aosta', 'aosta', 'AO', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (8, 102, 7, 'Imperia', 'imperia', 'IM', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (9, 102, 7, 'Savona', 'savona', 'SV', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (10, 102, 7, 'Genova', 'genova', 'GE', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (11, 102, 7, 'La Spezia', 'la-spezia', 'SP', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (12, 102, 3, 'Varese', 'varese', 'VA', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (13, 102, 3, 'Como', 'como', 'CO', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (14, 102, 3, 'Sondrio', 'sondrio', 'SO', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (15, 102, 3, 'Milano', 'milano', 'MI', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (16, 102, 3, 'Bergamo', 'bergamo', 'BG', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (17, 102, 3, 'Brescia', 'brescia', 'BS', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (18, 102, 3, 'Pavia', 'pavia', 'PV', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (19, 102, 3, 'Cremona', 'cremona', 'CR', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (20, 102, 3, 'Mantova', 'mantova', 'MN', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (21, 102, 4, 'Bolzano', 'bolzano', 'BZ', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (22, 102, 4, 'Trento', 'trento', 'TN', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (23, 102, 5, 'Verona', 'verona', 'VR', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (24, 102, 5, 'Vicenza', 'vicenza', 'VI', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (25, 102, 5, 'Belluno', 'belluno', 'BL', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (26, 102, 5, 'Treviso', 'treviso', 'TV', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (27, 102, 5, 'Venezia', 'venezia', 'VE', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (28, 102, 5, 'Padova', 'padova', 'PD', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (29, 102, 5, 'Rovigo', 'rovigo', 'RO', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (30, 102, 6, 'Udine', 'udine', 'UD', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (31, 102, 6, 'Gorizia', 'gorizia', 'GO', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (32, 102, 6, 'Trieste', 'trieste', 'TS', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (33, 102, 8, 'Piacenza', 'piacenza', 'PC', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (34, 102, 8, 'Parma', 'parma', 'PR', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (35, 102, 8, 'Reggio Emilia', 'reggio-emilia', 'RE', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (36, 102, 8, 'Modena', 'modena', 'MO', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (37, 102, 8, 'Bologna', 'bologna', 'BO', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (38, 102, 8, 'Ferrara', 'ferrara', 'FE', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (39, 102, 8, 'Ravenna', 'ravenna', 'RA', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (40, 102, 8, 'Forlì Cesena', 'forli-cesena', 'FC', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (41, 102, 11, 'Pesaro Urbino', 'pesaro-urbino', 'PU', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (42, 102, 11, 'Ancona', 'ancona', 'AN', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (43, 102, 11, 'Macerata', 'macerata', 'MC', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (44, 102, 11, 'Ascoli Piceno', 'ascoli-piceno', 'AP', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (45, 102, 9, 'Massa Carrara', 'massa-carrara', 'MS', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (46, 102, 9, 'Lucca', 'lucca', 'LU', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (47, 102, 9, 'Pistoia', 'pistoia', 'PT', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (48, 102, 9, 'Firenze', 'firenze', 'FI', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (49, 102, 9, 'Livorno', 'livorno', 'LI', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (50, 102, 9, 'Pisa', 'pisa', 'PI', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (51, 102, 9, 'Arezzo', 'arezzo', 'AR', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (52, 102, 9, 'Siena', 'siena', 'SI', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (53, 102, 9, 'Grosseto', 'grosseto', 'GR', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (54, 102, 10, 'Perugia', 'perugia', 'PG', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (55, 102, 10, 'Terni', 'terni', 'TR', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (56, 102, 12, 'Viterbo', 'viterbo', 'VT', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (57, 102, 12, 'Rieti', 'rieti', 'RI', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (58, 102, 12, 'Roma', 'roma', 'RM', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (59, 102, 12, 'Latina', 'latina', 'LT', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (60, 102, 12, 'Frosinone', 'frosinone', 'FR', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (61, 102, 15, 'Caserta', 'caserta', 'CE', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (62, 102, 15, 'Benevento', 'benevento', 'BN', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (63, 102, 15, 'Napoli', 'napoli', 'NA', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (64, 102, 15, 'Avellino', 'avellino', 'AV', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (65, 102, 15, 'Salerno', 'salerno', 'SA', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (66, 102, 13, 'L''Aquila', 'l-aquila', 'AQ', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (67, 102, 13, 'Teramo', 'teramo', 'TE', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (68, 102, 13, 'Pescara', 'pescara', 'PE', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (69, 102, 13, 'Chieti', 'chieti', 'CH', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (70, 102, 14, 'Campobasso', 'campobasso', 'CB', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (71, 102, 16, 'Foggia', 'foggia', 'FG', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (72, 102, 16, 'Bari', 'bari', 'BA', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (73, 102, 16, 'Taranto', 'taranto', 'TA', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (74, 102, 16, 'Brindisi', 'brindisi', 'BR', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (75, 102, 16, 'Lecce', 'lecce', 'LE', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (76, 102, 17, 'Potenza', 'potenza', 'PZ', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (77, 102, 17, 'Matera', 'matera', 'MT', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (78, 102, 18, 'Cosenza', 'cosenza', 'CS', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (79, 102, 18, 'Catanzaro', 'catanzaro', 'CZ', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (80, 102, 18, 'Reggio di Calabria', 'reggio-di-calabria', 'RC', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (81, 102, 19, 'Trapani', 'trapani', 'TP', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (82, 102, 19, 'Palermo', 'palermo', 'PA', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (83, 102, 19, 'Messina', 'messina', 'ME', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (84, 102, 19, 'Agrigento', 'agrigento', 'AG', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (85, 102, 19, 'Caltanissetta', 'caltanissetta', 'CL', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (86, 102, 19, 'Enna', 'enna', 'EN', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (87, 102, 19, 'Catania', 'catania', 'CT', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (88, 102, 19, 'Ragusa', 'ragusa', 'RG', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (89, 102, 19, 'Siracusa', 'siracusa', 'SR', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (90, 102, 20, 'Sassari', 'sassari', 'SS', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (91, 102, 20, 'Nuoro', 'nuoro', 'NU', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (92, 102, 20, 'Cagliari', 'cagliari', 'CA', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (93, 102, 6, 'Pordenone', 'pordenone', 'PN', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (94, 102, 14, 'Isernia', 'isernia', 'IS', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (95, 102, 20, 'Oristano', 'oristano', 'OR', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (96, 102, 1, 'Biella', 'biella', 'BI', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (97, 102, 3, 'Lecco', 'lecco', 'LC', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (98, 102, 3, 'Lodi', 'lodi', 'LO', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (99, 102, 8, 'Rimini', 'rimini', 'RN', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (100, 102, 9, 'Prato', 'prato', 'PO', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (101, 102, 18, 'Crotone', 'crotone', 'KR', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (102, 102, 18, 'Vibo Valentia', 'vibo-valentia', 'VV', 'S', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (103, 102, 1, 'Verbano Cusio Ossola', 'verbano-cusio-ossola', 'VB', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (104, 102, 20, 'Olbia Tempio', 'olbia-tempio', 'OT', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (105, 102, 20, 'Ogliastra', 'ogliastra', 'OG', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (106, 102, 20, 'Medio Campidano', 'medio-campidano', 'VS', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (107, 102, 20, 'Carbonia Iglesias', 'carbonia-iglesias', 'CI', 'I', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (108, 102, 3, 'Monza e della Brianza', 'monza-e-della-brianza', 'MB', 'N', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (109, 102, 11, 'Fermo', 'fermo', 'FM', 'C', '', 0, 0, '0');
INSERT INTO support_province (ID, ID_state, ID_region, name, smart_url, sigla, zone, coord_title, coord_lat, coord_lng, coord_zoom) VALUES (110, 102, 16, 'Barletta Andria Trani', 'barletta-andria-trani', 'BT', 'S', '', 0, 0, '0');