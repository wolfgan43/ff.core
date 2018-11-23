create table access_devices
(
  ID int auto_increment
    primary key,
  client_id varchar(255) null,
  ID_user int null,
  name varchar(255) null,
  type varchar(60) null,
  last_update int(10) null,
  hits int null,
  ips text null
)
  engine=InnoDB
;

create index client_id
  on access_devices (client_id)
;

create index `access_devices.ID_user`
  on access_devices (ID_user)
;

create table access_groups
(
  ID int auto_increment
    primary key,
  name varchar(255) null,
  level int null,
  profile varchar(255) not null
)
  engine=InnoDB
;

create table access_tokens
(
  ID int auto_increment
    primary key,
  ID_user int null,
  type varchar(255) null,
  token varchar(255) null,
  expire int(10) null,
  refresh_token varchar(255) null,
  ID_remote varchar(255) null
)
  engine=InnoDB
;

create index `access_tokens.ID_user`
  on access_tokens (ID_user)
;

create index type
  on access_tokens (type)
;

create index token
  on access_tokens (token)
;

create index expire
  on access_tokens (expire)
;

create index ID_remote
  on access_tokens (ID_remote)
;

create table access_users
(
  ID int auto_increment
    primary key,
  ID_domain int null,
  acl int null comment 'Old Field primry_gid',
  acl_primary varchar(15) null,
  acl_profile varchar(255) null,
  expire int(10) null comment 'Old field expiration datetime',
  status int(1) null,
  username varchar(255) null,
  username_slug varchar(255) null,
  email varchar(255) null,
  tel varchar(20) null,
  password varchar(64) null,
  avatar varchar(255) null,
  created int(10) null,
  last_update int(10) null,
  last_login int(10) null,
  ID_lang int null,
  SID varchar(255) null comment 'Old field activation_code',
  SID_expire int(10) null,
  SID_device int null,
  SID_ip varchar(15) null,
  SID_question varchar(60) null,
  SID_answer varchar(64) null,
  verified_email int(10) null,
  verified_tel int(10) null,
  modified datetime null,
  constraint `access_users.ID_group`
  foreign key (acl) references access_groups (ID)
)
  engine=InnoDB
;

create index ID_domain
  on access_users (ID_domain)
;

create index ID_group
  on access_users (acl)
;

create index expire
  on access_users (expire)
;

create index status
  on access_users (status)
;

create index username
  on access_users (username)
;

create index username_slug
  on access_users (username_slug)
;

create index email
  on access_users (email)
;

create index tel
  on access_users (tel)
;

create index SID
  on access_users (SID)
;

alter table access_devices
  add constraint `access_devices.ID_user`
foreign key (ID_user) references access_users (ID)
;

alter table access_tokens
  add constraint `access_tokens.ID_user`
foreign key (ID_user) references access_users (ID)
;

create table access_users_groups
(
  ID int auto_increment
    primary key,
  ID_user int null,
  ID_group int null,
  constraint `access_users_groups.ID_user`
  foreign key (ID_user) references access_users (ID),
  constraint `access_users_groups.ID_group`
  foreign key (ID_group) references access_groups (ID)
)
  engine=InnoDB
;

create index gid
  on access_users_groups (ID_user, ID_group)
;

create index ID_user
  on access_users_groups (ID_user)
;

create index `access_users_groups.ID_group`
  on access_users_groups (ID_group)
;

create table domains
(
  ID int auto_increment
    primary key,
  name varchar(255) null,
  langs varchar(255) null,
  expire int(10) null,
  created int(10) null,
  status int(1) null,
  version varchar(20) null,
  last_update int(10) null,
  owner int null,
  ip varchar(15) null,
  scopes varchar(2000) null,
  secret varchar(255) null,
  company_name varchar(50) null,
  company_description varchar(255) null,
  company_state varchar(50) null,
  company_province varchar(50) null,
  company_city varchar(50) null,
  company_email varchar(50) null,
  company_ID_place int null
)
  engine=InnoDB
;

alter table access_users
  add constraint `access_users.ID_domain`
foreign key (ID_domain) references domains (ID)
;

create table domains_access
(
  ID int auto_increment
    primary key,
  host varchar(255) null,
  name varchar(255) null,
  user varchar(255) null,
  password varchar(255) null,
  type varchar(10) null comment 'mysql, mongo, ecc',
  tables text null comment 'list of tables trust separated with dot',
  ID_domain int null,
  constraint `domains_access.ID_domain`
  foreign key (ID_domain) references domains (ID)
)
  engine=InnoDB
;

create index ID_domain_idx
  on domains_access (ID_domain)
;

create table domains_policy
(
  ID int auto_increment
    primary key,
  ID_domain int null,
  ID_group int null,
  groups varchar(255) null,
  scopes varchar(2000) null,
  constraint `domains_policy.ID_domain`
  foreign key (ID_domain) references domains (ID),
  constraint `domains_policy.ID_group`
  foreign key (ID_group) references access_groups (ID)
)
  engine=InnoDB
;

create index ID_domain
  on domains_policy (ID_domain)
;

create index ID_group_idx
  on domains_policy (ID_group)
;

create table domains_policy_granted
(
  ID int auto_increment
    primary key,
  ID_domain int null,
  ID_user_trusted int null,
  ID_user_shared int null,
  client_id varchar(80) null,
  ID_device int null,
  scope varchar(15) null,
  expire int(10) null,
  created int(10) null,
  last_update int(10) null,
  constraint `domains_policy_granted.ID_domain`
  foreign key (ID_domain) references domains (ID),
  constraint `domains_policy_granted.ID_user_trusted`
  foreign key (ID_user_trusted) references access_users (ID),
  constraint `domains_policy_granted.ID_user_shared`
  foreign key (ID_user_shared) references access_users (ID),
  constraint `domains_policy_granted.ID_device`
  foreign key (ID_device) references access_devices (ID)
)
  engine=InnoDB
;

create index ID_domain
  on domains_policy_granted (ID_domain)
;

create index ID_user_trusted_idx
  on domains_policy_granted (ID_user_trusted)
;

create index ID_user_shared_idx
  on domains_policy_granted (ID_user_shared)
;

create index client_id_idx
  on domains_policy_granted (client_id)
;

create index ID_device_idx
  on domains_policy_granted (ID_device)
;

create table domains_privacy
(
  ID int not null
    primary key,
  ID_domain int null,
  description text null,
  version int(4) null,
  created int(10) null,
  last_update int(10) null,
  type varchar(15) null,
  title varchar(255) null,
  constraint domain_privacy_ID_domains
  foreign key (ID_domain) references domains (ID)
)
  engine=InnoDB
;

create index domain_privacy_ID_domains_idx
  on domains_privacy (ID_domain)
;

create table domains_registration
(
  ID int auto_increment
    primary key,
  ID_domain int null,
  ID_group int null,
  anagraph_type varchar(15) null,
  token int(1) null,
  activation varchar(10) null,
  expire int(10) default '0' not null,
  constraint `domains_registration.ID_domain`
  foreign key (ID_domain) references domains (ID),
  constraint `domains_registration.ID_group`
  foreign key (ID_group) references access_groups (ID)
)
  engine=InnoDB
;

create index ID_domain
  on domains_registration (ID_domain)
;

create index ID_group_idx
  on domains_registration (ID_group)
;

create table domains_security
(
  ID int auto_increment
    primary key,
  ID_domain int null,
  csr_url varchar(255) null,
  csr_ip varchar(15) null,
  csr_protocol varchar(5) null,
  pkey_url varchar(255) null,
  pkey_ip varchar(15) null,
  pkey_protocol varchar(15) null,
  cert_expire int(3) null,
  cert_alg varchar(10) null,
  cert_id_length int(2) null,
  cert_key_length int(2) null,
  cert_precision int(2) null,
  token_expire int(10) null,
  token_type varchar(15) null,
  sa_alg varchar(15) null,
  sa_expire int(4) null,
  sa_sender varchar(15) null,
  sa_human varchar(15) null,
  pw_hash varchar(15) null,
  pw_validator varchar(15) null,
  constraint `domains_security.ID_domain`
  foreign key (ID_domain) references domains (ID)
)
  engine=InnoDB
;

create index ID_domain
  on domains_security (ID_domain)
;

create table domains_settings
(
  ID int auto_increment
    primary key,
  ID_domain int null,
  name varchar(50) null,
  value varchar(255) null,
  constraint `domains_settings.ID_domain`
  foreign key (ID_domain) references domains (ID)
)
  engine=InnoDB
;

create index ID_domain
  on domains_settings (ID_domain)
;

create table oauth_access_tokens
(
  access_token varchar(40) not null
    primary key,
  client_id varchar(80) null,
  user_id varchar(255) null,
  expires timestamp null,
  scope varchar(2000) null
)
  engine=InnoDB
;

create table oauth_authorization_codes
(
  authorization_code varchar(40) not null
    primary key,
  client_id varchar(80) null,
  user_id varchar(255) null,
  redirect_uri varchar(2000) null,
  expires timestamp null,
  scope varchar(2000) null,
  sso_state varchar(255) null
)
  engine=InnoDB
;

create table oauth_clients
(
  client_id varchar(80) not null
    primary key,
  client_secret varchar(80) null,
  redirect_uri varchar(2000) null,
  grant_types varchar(80) null,
  ID_grant_type int null,
  scope varchar(100) null,
  description text null,
  disable_csrf tinyint(1) null,
  sso tinyint(1) null,
  url_site varchar(255) null,
  url_privacy varchar(255) null,
  json_only tinyint(1) null,
  domains varchar(255) null
)
  engine=InnoDB
;

create index ID_grant_type_idx
  on oauth_clients (ID_grant_type)
;

alter table access_devices
  add constraint `access_devices.client_id`
foreign key (client_id) references oauth_clients (client_id)
;

alter table domains_policy_granted
  add constraint `domains_policy_granted.client_id`
foreign key (client_id) references oauth_clients (client_id)
;

create table oauth_grant_types
(
  ID int not null
    primary key,
  name varchar(255) null,
  grant_type varchar(255) null
)
  engine=InnoDB
;

alter table oauth_clients
  add constraint `oauth_clients.ID_grant_type`
foreign key (ID_grant_type) references oauth_grant_types (ID)
;

create table oauth_jwt
(
  client_id varchar(80) not null
    primary key,
  subject varchar(80) null,
  public_key varchar(2000) null
)
  engine=InnoDB
;

create table oauth_refresh_tokens
(
  refresh_token varchar(40) not null
    primary key,
  client_id varchar(80) null,
  user_id varchar(255) null,
  expires timestamp null,
  scope varchar(2000) null
)
  engine=InnoDB
;

create table oauth_rel_users
(
  ID_user int not null,
  client_id varchar(80) not null,
  granted tinyint(1) null,
  `when` datetime null,
  `by` varchar(255) null,
  primary key (ID_user, client_id)
)
  engine=InnoDB
;

create table oauth_scopes
(
  ID int auto_increment
    primary key,
  scope varchar(255) null,
  is_default tinyint(1) null,
  description text null,
  special tinyint(1) null,
  ID_domain int null,
  constraint ID_domain
  foreign key (ID_domain) references domains (ID)
)
  engine=InnoDB
;

create index scope
  on oauth_scopes (scope)
;

create index ID_domain
  on oauth_scopes (ID_domain)
;

