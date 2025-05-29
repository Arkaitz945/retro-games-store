INSERT INTO consolas (
    nombre,
    fabricante,
    año_lanzamiento,
    descripcion,
    estado,
    precio,
    stock,
    imagen
) VALUES
(
    'PlayStation 1',
    'Sony',
    1994,
    'Primera consola de Sony, pionera en el uso de CDs para juegos.',
    'Usada',
    79.99,
    5,
    'ps1.jpg'
),
(
    'PlayStation 2',
    'Sony',
    2000,
    'Consola más vendida de la historia, compatible con DVD y juegos PS1.',
    'Usada',
    99.99,
    7,
    'ps2.jpg'
),
(
    'Super Nintendo Entertainment System (SNES)',
    'Nintendo',
    1990,
    'Consola de 16 bits con clásicos como Super Mario World y Zelda.',
    'Usada',
    89.99,
    4,
    'snes.jpg'
),
(
    'Nintendo 64',
    'Nintendo',
    1996,
    'Primera consola de Nintendo con gráficos en 3D reales y joystick analógico.',
    'Usada',
    89.99,
    6,
    'n64.jpg'
),
(
    'Sega Genesis',
    'Sega',
    1988,
    'Consola de 16 bits conocida por Sonic the Hedgehog y su velocidad.',
    'Usada',
    69.99,
    3,
    'genesis.jpg'
),
(
    'Atari 2600',
    'Atari',
    1977,
    'Una de las primeras consolas caseras, pionera en el mercado de videojuegos.',
    'Colección',
    129.99,
    2,
    'atari2600.jpg'
),
(
    'GameCube',
    'Nintendo',
    2001,
    'Compacta consola de Nintendo con discos mini-DVD y títulos populares.',
    'Usada',
    84.99,
    5,
    'gamecube.jpg'
),
(
    'Dreamcast',
    'Sega',
    1998,
    'Última consola de Sega, innovadora con capacidades online integradas.',
    'Usada',
    89.99,
    2,
    'dreamcast.jpg'
);



INSERT INTO juegos (
    nombre,
    plataforma,
    genero,
    año_lanzamiento,
    desarrollador,
    publisher,
    estado,
    precio,
    stock,
    descripcion,
    imagen,
    region,
    incluye_caja,
    incluye_manual
) VALUES
(
    'Final Fantasy VII',
    'PlayStation 1',
    'RPG',
    1997,
    'Square',
    'Sony',
    'Usado',
    39.99,
    5,
    'Juego icónico de rol con historia épica y gráficos prerenderizados.',
    'ff7_ps1.jpg',
    'NTSC',
    TRUE,
    TRUE
),
(
    'Super Mario World',
    'Super Nintendo',
    'Plataformas',
    1990,
    'Nintendo',
    'Nintendo',
    'Usado',
    34.99,
    3,
    'Aventura de Mario en Dinosaur Land, debut de Yoshi.',
    'smw_snes.jpg',
    'NTSC',
    TRUE,
    FALSE
),
(
    'The Legend of Zelda: Ocarina of Time',
    'Nintendo 64',
    'Aventura',
    1998,
    'Nintendo EAD',
    'Nintendo',
    'Usado',
    44.99,
    4,
    'Uno de los juegos más influyentes de todos los tiempos, con mundo 3D.',
    'zelda_oot_n64.jpg',
    'NTSC',
    TRUE,
    TRUE
),
(
    'Sonic the Hedgehog',
    'Sega Genesis',
    'Plataformas',
    1991,
    'Sonic Team',
    'Sega',
    'Usado',
    29.99,
    6,
    'Velocidad y acción con el erizo azul más famoso.',
    'sonic_genesis.jpg',
    'NTSC',
    FALSE,
    TRUE
),
(
    'Metal Gear Solid',
    'PlayStation 1',
    'Acción / Sigilo',
    1998,
    'Konami',
    'Konami',
    'Usado',
    39.99,
    5,
    'Juego de sigilo táctico con narrativa cinematográfica.',
    'mgs_ps1.jpg',
    'PAL',
    TRUE,
    TRUE
);


INSERT INTO revistas (
    titulo,
    editorial,
    fecha_publicacion,
    descripcion,
    precio,
    stock,
    imagen
) VALUES
(
    'Club Nintendo - Edición Especial Zelda',
    'Nintendo',
    '1998-12-01',
    'Edición dedicada a Ocarina of Time con trucos, mapas y entrevistas.',
    9.99,
    2,
    'club_nintendo_zelda.jpg'
),
(
    'Hobby Consolas Nº 100',
    'Hobby Press',
    '2000-01-15',
    'Número conmemorativo que repasa los 100 mejores juegos de la década.',
    7.99,
    4,
    'hobbyconsolas_100.jpg'
),
(
    'Edge Magazine - Retro Issue',
    'Future Publishing',
    '2005-06-01',
    'Número dedicado a la historia de las consolas clásicas.',
    11.99,
    3,
    'edge_retro.jpg'
),
(
    'Superjuegos - Especial PSX',
    'Grupo Zeta',
    '1999-10-20',
    'Reportajes sobre la PlayStation 1 y sus mejores juegos.',
    8.49,
    5,
    'superjuegos_psx.jpg'
),
(
    'GamePro - Mortal Kombat II Cover',
    'IDG',
    '1994-04-10',
    'Edición con cobertura exclusiva de Mortal Kombat II.',
    6.99,
    2,
    'gamepro_mk2.jpg'
);
