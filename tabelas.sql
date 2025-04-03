CREATE TABLE Anunciante
(
    id INT PRIMARY KEY auto_increment,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(15) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NOT,
    senhaHash VARCHAR(50) NOT NULL,
    telefone VARCHAR(20) UNIQUE NOT NULL
) ENGINE=InnoDB;

CREATE TABLE Anuncio
(
    id INT PRIMARY KEY auto_increment,
    marca VARCHAR(30) NOT NULL,
    modelo VARCHAR(30) NOT NULL,
    ano INT NOT NULL,
    cor VARCHAR(30) NOT NULL,
    quilometragem INT NOT NULL,
    descricao VARCHAR(1000),
    valor FLOAT NOT NULL,
    dataHora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(50) NOT NULL,
    cidade VARCHAR(50) NOT NULL,
    idAnunciante INT FOREIGN KEY REFERENCES Anunciante (id)
) ENGINE=InnoDB;


CREATE TABLE Interesse
(
    id INT PRIMARY KEY auto_increment,
    nome VARCHAR(100) NOT NULL,
    telfone VARCHAR(20) NOT NULL,
    mensagem VARCHAR(1000) NOT NULL,
    dataHora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    idAnuncio INT FOREIGN KEY REFERENCES Anuncio (id)
) ENGINE=InnoDB;


CREATE TABLE Foto
(
    idAnuncio INT FOREIGN KEY REFERENCES Anuncio (id),
    nomeArqFoto VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

