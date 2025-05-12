-- Insertar datos de ejemplo en la tabla juegos

INSERT INTO juegos (nombre, plataforma, genero, año_lanzamiento, desarrollador, publisher, estado, precio, stock, descripcion, imagen, region, incluye_caja, incluye_manual) VALUES 
-- Nintendo
('Super Mario Bros.', 'NES', 'Plataformas', 1985, 'Nintendo', 'Nintendo', 'Usado', 39.99, 5, 'El clásico juego de plataformas que definió un género. Controla a Mario en su misión para rescatar a la Princesa Peach de las garras de Bowser.', 'img/products/super-mario-bros.jpg', 'PAL', 1, 1),
('The Legend of Zelda: Ocarina of Time', 'Nintendo 64', 'Aventura', 1998, 'Nintendo EAD', 'Nintendo', 'Buen estado', 69.99, 3, 'Considerado uno de los mejores juegos de todos los tiempos, Ocarina of Time revolucionó los juegos de aventura en 3D.', 'img/products/zelda-ocarina.jpg', 'PAL', 1, 1),
('Super Mario 64', 'Nintendo 64', 'Plataformas', 1996, 'Nintendo EAD', 'Nintendo', 'Coleccionista', 59.99, 2, 'El primer juego de Mario en 3D que reinventó el género de plataformas para la era de los gráficos tridimensionales.', 'img/products/super-mario-64.jpg', 'NTSC', 1, 0),
('Pokémon Rojo', 'Game Boy', 'RPG', 1996, 'Game Freak', 'Nintendo', 'Usado', 49.99, 4, 'El inicio de la fiebre Pokémon. Captura, entrena y combate con 151 criaturas diferentes en tu camino para convertirte en Maestro Pokémon.', 'img/products/pokemon-rojo.jpg', 'PAL', 0, 0),

-- Sega
('Sonic the Hedgehog 2', 'Sega Mega Drive', 'Plataformas', 1992, 'Sonic Team', 'Sega', 'Restaurado', 44.99, 6, 'La secuela que mejoró la fórmula del erizo azul, introduciendo a Tails y el modo cooperativo.', 'img/products/sonic-2.jpg', 'PAL', 1, 1),
('Shenmue', 'Dreamcast', 'Aventura', 1999, 'Sega AM2', 'Sega', 'Como nuevo', 89.99, 1, 'Una aventura épica con un mundo abierto detallado que estaba muy adelantada a su tiempo.', 'img/products/shenmue.jpg', 'PAL', 1, 1),

-- PlayStation
('Final Fantasy VII', 'PlayStation', 'RPG', 1997, 'Square', 'Sony Computer Entertainment', 'Usado', 79.99, 3, 'El RPG que definió una generación, con una historia épica, personajes memorables y un sistema de combate innovador.', 'img/products/ff7.jpg', 'PAL', 1, 1),
('Metal Gear Solid', 'PlayStation', 'Acción/Sigilo', 1998, 'Konami Computer Entertainment Japan', 'Konami', 'Buen estado', 59.99, 4, 'El juego que estableció el género de sigilo moderno, con una narrativa cinematográfica y jugabilidad innovadora.', 'img/products/metal-gear-solid.jpg', 'PAL', 1, 0),
('Resident Evil 2', 'PlayStation', 'Survival Horror', 1998, 'Capcom', 'Capcom', 'Coleccionista', 89.99, 1, 'Una obra maestra del horror de supervivencia que expandió la historia de Raccoon City con dos protagonistas jugables.', 'img/products/resident-evil-2.jpg', 'NTSC-J', 1, 1),
('Crash Bandicoot', 'PlayStation', 'Plataformas', 1996, 'Naughty Dog', 'Sony Computer Entertainment', 'Usado', 39.99, 5, 'El marsupial mascota no oficial de PlayStation en su aventura debut para detener al Dr. Neo Cortex.', 'img/products/crash-bandicoot.jpg', 'PAL', 0, 0),

-- SNES  
('Chrono Trigger', 'Super Nintendo', 'RPG', 1995, 'Square', 'Square', 'Buen estado', 199.99, 1, 'Un RPG legendario con múltiples finales, sistema de combate innovador y una historia que trasciende el tiempo.', 'img/products/chrono-trigger.jpg', 'NTSC', 0, 0),
('Super Metroid', 'Super Nintendo', 'Acción/Aventura', 1994, 'Nintendo R&D1', 'Nintendo', 'Restaurado', 129.99, 2, 'La tercera entrega de la serie Metroid, considerada como una de las mejores de la franquicia y un referente en el género metroidvania.', 'img/products/super-metroid.jpg', 'PAL', 1, 1),
('Street Fighter II Turbo', 'Super Nintendo', 'Lucha', 1993, 'Capcom', 'Capcom', 'Usado', 49.99, 3, 'La versión mejorada del revolucionario juego de lucha, con todos los personajes jugables y mayor velocidad.', 'img/products/street-fighter-2.jpg', 'PAL', 1, 0),

-- Game Boy y Game Boy Color
('Pokémon Cristal', 'Game Boy Color', 'RPG', 2001, 'Game Freak', 'Nintendo', 'Usado', 79.99, 2, 'La versión mejorada de Pokémon Oro y Plata con características exclusivas y la primera opción de jugar como chica.', 'img/products/pokemon-crystal.jpg', 'PAL', 1, 1),
('The Legend of Zelda: Link\'s Awakening DX', 'Game Boy Color', 'Aventura', 1998, 'Nintendo', 'Nintendo', 'Buen estado', 59.99, 3, 'La versión a color del clásico de Game Boy, con una mazmorra adicional y compatibilidad con impresora Game Boy.', 'img/products/links-awakening-dx.jpg', 'PAL', 0, 0),
('Tetris', 'Game Boy', 'Puzzle', 1989, 'Nintendo', 'Nintendo', 'Usado', 29.99, 7, 'El juego que catapultó la popularidad de Game Boy y definió el género de puzzles para siempre.', 'img/products/tetris-gb.jpg', 'PAL', 0, 0),

-- Game Boy Advance
('Pokemon Esmeralda', 'Game Boy Advance', 'RPG', 2004, 'Game Freak', 'Nintendo', 'Buen estado', 69.99, 4, 'La versión definitiva de la tercera generación Pokémon con una historia ampliada y la Battle Frontier.', 'img/products/pokemon-emerald.jpg', 'PAL', 1, 0),
('Castlevania: Aria of Sorrow', 'Game Boy Advance', 'Acción/Plataformas', 2003, 'Konami', 'Konami', 'Como nuevo', 89.99, 1, 'Una de las mejores entregas de la saga Castlevania con un sistema único de absorción de almas.', 'img/products/aria-of-sorrow.jpg', 'PAL', 1, 1),
('Golden Sun', 'Game Boy Advance', 'RPG', 2001, 'Camelot Software Planning', 'Nintendo', 'Usado', 44.99, 3, 'Un RPG épico con impresionantes gráficos para GBA y un innovador sistema de psinergias.', 'img/products/golden-sun.jpg', 'PAL', 0, 0),

-- PlayStation 2
('God of War II', 'PlayStation 2', 'Acción/Aventura', 2007, 'Santa Monica Studio', 'Sony Computer Entertainment', 'Buen estado', 29.99, 5, 'La épica continuación de las aventuras de Kratos en su búsqueda de venganza contra los dioses del Olimpo.', 'img/products/god-of-war-2.jpg', 'PAL', 1, 1),
('Shadow of the Colossus', 'PlayStation 2', 'Acción/Aventura', 2005, 'Team Ico', 'Sony Computer Entertainment', 'Coleccionista', 69.99, 2, 'Una obra maestra artística donde el jugador se enfrenta a 16 colosos gigantes en un mundo desolado.', 'img/products/shadow-colossus.jpg', 'PAL', 1, 1),
('Silent Hill 2', 'PlayStation 2', 'Survival Horror', 2001, 'Konami', 'Konami', 'Usado', 79.99, 1, 'Una de las experiencias de terror psicológico más perturbadoras e influyentes en la historia de los videojuegos.', 'img/products/silent-hill-2.jpg', 'PAL', 1, 0),

-- Xbox
('Halo: Combat Evolved', 'Xbox', 'FPS', 2001, 'Bungie', 'Microsoft Game Studios', 'Buen estado', 24.99, 6, 'El juego que definió la marca Xbox y revolucionó los shooters en consola con su modo campaña y multijugador.', 'img/products/halo.jpg', 'PAL', 1, 1),
('Fable', 'Xbox', 'RPG', 2004, 'Lionhead Studios', 'Microsoft Game Studios', 'Usado', 19.99, 3, 'Un RPG donde tus decisiones afectan al mundo y a la apariencia del personaje, con un rico mundo de fantasía.', 'img/products/fable.jpg', 'PAL', 1, 0),
('Jet Set Radio Future', 'Xbox', 'Acción/Deportes', 2002, 'Smilebit', 'Sega', 'Coleccionista', 59.99, 1, 'Secuela del innovador juego de Dreamcast, con un estilo cel-shading único y una excelente banda sonora.', 'img/products/jsrf.jpg', 'NTSC', 1, 1),

-- GameCube
('The Legend of Zelda: Wind Waker', 'GameCube', 'Aventura', 2002, 'Nintendo EAD', 'Nintendo', 'Buen estado', 69.99, 2, 'La aventura de Link con un estilo cel-shading que fue controvertido en su lanzamiento pero ahora es reconocido como atemporal.', 'img/products/wind-waker.jpg', 'PAL', 1, 1),
('Metroid Prime', 'GameCube', 'Acción/Aventura', 2002, 'Retro Studios', 'Nintendo', 'Usado', 49.99, 3, 'La exitosa transición de Metroid al 3D, combinando exploración, plataformas y combate en primera persona.', 'img/products/metroid-prime.jpg', 'PAL', 1, 0),
('Resident Evil 4', 'GameCube', 'Survival Horror', 2005, 'Capcom', 'Capcom', 'Como nuevo', 59.99, 2, 'El juego que reinventó la saga Resident Evil con una perspectiva en tercera persona y acción más directa.', 'img/products/re4-gc.jpg', 'PAL', 1, 1),

-- Sega Saturn
('NiGHTS into Dreams', 'Sega Saturn', 'Acción', 1996, 'Sonic Team', 'Sega', 'Coleccionista', 119.99, 1, 'Una experiencia de vuelo onírica única con un sistema de juego innovador y visuales impresionantes para su época.', 'img/products/nights.jpg', 'NTSC-J', 1, 1),
('Panzer Dragoon Saga', 'Sega Saturn', 'RPG', 1998, 'Team Andromeda', 'Sega', 'Coleccionista', 499.99, 1, 'Uno de los RPGs más raros y valorados, combinando combates por turnos con secuencias de vuelo.', 'img/products/panzer-dragoon-saga.jpg', 'NTSC-J', 1, 1),

-- Neo Geo
('Metal Slug', 'Neo Geo', 'Run and Gun', 1996, 'Nazca Corporation', 'SNK', 'Coleccionista', 299.99, 1, 'El primer juego de la legendaria serie de acción con gráficos dibujados a mano y humor característico.', 'img/products/metal-slug.jpg', 'AES', 1, 1),
('The King of Fighters \'98', 'Neo Geo', 'Lucha', 1998, 'SNK', 'SNK', 'Usado', 199.99, 1, 'Considerado por muchos como la mejor entrega de la serie KOF, con un roster extenso de luchadores.', 'img/products/kof-98.jpg', 'MVS', 0, 0),

-- Atari 2600
('Pitfall!', 'Atari 2600', 'Plataformas', 1982, 'Activision', 'Activision', 'Usado', 19.99, 3, 'Uno de los primeros juegos de plataformas y exploración, estableciendo muchas convenciones del género.', 'img/products/pitfall.jpg', 'NTSC', 0, 0),
('Space Invaders', 'Atari 2600', 'Arcade', 1980, 'Taito', 'Atari', 'Usado', 15.99, 4, 'La adaptación doméstica del clásico arcade que ayudó a establecer los shooters como género principal.', 'img/products/space-invaders-2600.jpg', 'NTSC', 0, 0),

-- PC Engine/TurboGrafx-16
('Castlevania: Rondo of Blood', 'PC Engine CD', 'Plataformas', 1993, 'Konami', 'Konami', 'Coleccionista', 249.99, 1, 'Una de las mejores entregas de Castlevania, exclusiva durante mucho tiempo para PC Engine CD en Japón.', 'img/products/rondo-of-blood.jpg', 'NTSC-J', 1, 1),
('Bomberman \'94', 'PC Engine', 'Acción', 1993, 'Hudson Soft', 'Hudson Soft', 'Buen estado', 89.99, 1, 'Una de las mejores versiones de Bomberman con excelente modo multijugador y gráficos coloridos.', 'img/products/bomberman-94.jpg', 'NTSC-J', 0, 0);
