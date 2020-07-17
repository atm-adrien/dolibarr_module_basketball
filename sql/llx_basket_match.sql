create table llx_basket_match
(
    rowid           integer AUTO_INCREMENT PRIMARY KEY,
    ref             varchar(128) NOT NULL,
    nom             varchar(255) NOT NULL,
    fk_soc1         varchar(255) NOT NULL,
    fk_soc2         varchar(255) NOT NULL,
    tarif           decimal(5,2) NOT NULL,
    date            datetime NOT NULL,
    terrain         varchar(255) NOT NULL
);

create table llx_c_terrain
(
    rowid           integer AUTO_INCREMENT PRIMARY KEY,
    code            varchar(3) NOT NULL,
    nom_terrain     varchar(255) NOT NULL,
    ville           varchar(255) NOT NULL,
    active          int NOT NULL
)
