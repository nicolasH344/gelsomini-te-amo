-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: cursinho
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `exercises`
--

DROP TABLE IF EXISTS `exercises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercises` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced') COLLATE utf8mb4_unicode_ci DEFAULT 'beginner',
  `exercise_type` enum('html','css','javascript','php','mixed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `initial_code` text COLLATE utf8mb4_unicode_ci,
  `solution_code` text COLLATE utf8mb4_unicode_ci,
  `instructions` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `hints` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `exercises_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `exercise_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `exercises_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercises`
--

LOCK TABLES `exercises` WRITE;
/*!40000 ALTER TABLE `exercises` DISABLE KEYS */;
INSERT INTO `exercises` VALUES (1,1,'Minha Primeira Página HTML','Crie uma página HTML básica com título e parágrafo','beginner','html','<!DOCTYPE html>\n<html>\n<head>\n    <title></title>\n</head>\n<body>\n    \n</body>\n</html>','<!DOCTYPE html>\n<html>\n<head>\n    <title>Minha Primeira Página</title>\n</head>\n<body>\n    <h1>Bem-vindo!</h1>\n    <p>Esta é minha primeira página HTML.</p>\n</body>\n</html>','1. Adicione um título à página dentro da tag <title>\n2. Crie um cabeçalho <h1> com o texto \"Bem-vindo!\"\n3. Adicione um parágrafo <p> com o texto \"Esta é minha primeira página HTML.\"','Use a tag <title> dentro do <head>\nUse <h1> para cabeçalhos principais\nUse <p> para parágrafos',1,'2025-09-08 17:27:41','2025-09-08 17:27:41'),(2,1,'Lista de Compras','Crie uma lista não ordenada com itens de compras','beginner','html','<!DOCTYPE html>\n<html>\n<head>\n    <title>Lista de Compras</title>\n</head>\n<body>\n    <h1>Minha Lista de Compras</h1>\n    <!-- Adicione sua lista aqui -->\n</body>\n</html>','<!DOCTYPE html>\n<html>\n<head>\n    <title>Lista de Compras</title>\n</head>\n<body>\n    <h1>Minha Lista de Compras</h1>\n    <ul>\n        <li>Pão</li>\n        <li>Leite</li>\n        <li>Ovos</li>\n        <li>Frutas</li>\n    </ul>\n</body>\n</html>','1. Use a tag <ul> para criar uma lista não ordenada\n2. Adicione pelo menos 4 itens usando <li>\n3. Inclua itens como: Pão, Leite, Ovos, Frutas','<ul> cria listas não ordenadas\n<li> define cada item da lista\nVocê pode adicionar quantos itens quiser',1,'2025-09-08 17:27:41','2025-09-08 17:27:41'),(3,1,'Formulário de Contato','Crie um formulário básico com campos de nome, email e mensagem','intermediate','html','<!DOCTYPE html>\n<html>\n<head>\n    <title>Contato</title>\n</head>\n<body>\n    <h1>Entre em Contato</h1>\n    <!-- Adicione o formulário aqui -->\n</body>\n</html>','<!DOCTYPE html>\n<html>\n<head>\n    <title>Contato</title>\n</head>\n<body>\n    <h1>Entre em Contato</h1>\n    <form>\n        <label for=\"nome\">Nome:</label>\n        <input type=\"text\" id=\"nome\" name=\"nome\" required><br><br>\n        \n        <label for=\"email\">Email:</label>\n        <input type=\"email\" id=\"email\" name=\"email\" required><br><br>\n        \n        <label for=\"mensagem\">Mensagem:</label><br>\n        <textarea id=\"mensagem\" name=\"mensagem\" rows=\"4\" cols=\"50\" required></textarea><br><br>\n        \n        <input type=\"submit\" value=\"Enviar\">\n    </form>\n</body>\n</html>','1. Crie um formulário usando <form>\n2. Adicione campos para nome (text), email (email) e mensagem (textarea)\n3. Use <label> para rotular cada campo\n4. Adicione um botão de envio','Use <form> para criar formulários\n<input type=\"text\"> para campos de texto\n<input type=\"email\"> para campos de email\n<textarea> para textos longos\n<input type=\"submit\"> para botão de envio',1,'2025-09-08 17:27:41','2025-09-08 17:27:41'),(4,2,'Estilizando Texto','Aplique estilos básicos a um parágrafo','beginner','css','p {\n    /* Adicione seus estilos aqui */\n}','p {\n    color: blue;\n    font-size: 18px;\n    font-weight: bold;\n    text-align: center;\n}','1. Mude a cor do texto para azul\n2. Defina o tamanho da fonte como 18px\n3. Torne o texto negrito\n4. Centralize o texto','Use color para cor do texto\nUse font-size para tamanho\nUse font-weight: bold para negrito\nUse text-align: center para centralizar',1,'2025-09-08 17:27:41','2025-09-08 17:27:41'),(5,2,'Layout com Flexbox','Crie um layout flexível com três caixas','intermediate','css','.container {\n    /* Adicione propriedades flexbox aqui */\n}\n\n.box {\n    width: 100px;\n    height: 100px;\n    background-color: lightblue;\n    margin: 10px;\n}','.container {\n    display: flex;\n    justify-content: space-around;\n    align-items: center;\n    height: 200px;\n}\n\n.box {\n    width: 100px;\n    height: 100px;\n    background-color: lightblue;\n    margin: 10px;\n}','1. Torne o container um flex container\n2. Distribua as caixas uniformemente\n3. Centralize as caixas verticalmente\n4. Defina uma altura para o container','display: flex ativa o flexbox\njustify-content controla alinhamento horizontal\nalign-items controla alinhamento vertical\nspace-around distribui espaço uniformemente',1,'2025-09-08 17:27:41','2025-09-08 17:27:41'),(6,3,'Olá Mundo JavaScript','Exiba uma mensagem no console','beginner','javascript','// Escreva seu código aqui','console.log(\"Olá, Mundo!\");','1. Use console.log() para exibir a mensagem \"Olá, Mundo!\"','console.log() exibe mensagens no console\nUse aspas para strings',1,'2025-09-08 17:27:41','2025-09-08 17:27:41'),(7,3,'Calculadora Simples','Crie uma função que soma dois números','beginner','javascript','function somar(a, b) {\n    // Complete a função\n}\n\n// Teste a função\nconsole.log(somar(5, 3));','function somar(a, b) {\n    return a + b;\n}\n\n// Teste a função\nconsole.log(somar(5, 3));','1. Complete a função somar para retornar a soma de a e b\n2. Use return para retornar o resultado','Use return a + b para somar\nO operador + soma números em JavaScript',1,'2025-09-08 17:27:41','2025-09-08 17:27:41'),(8,3,'Manipulação de Array','Trabalhe com arrays e loops','intermediate','javascript','const numeros = [1, 2, 3, 4, 5];\n// Calcule a soma de todos os números','const numeros = [1, 2, 3, 4, 5];\nlet soma = 0;\n\nfor (let i = 0; i < numeros.length; i++) {\n    soma += numeros[i];\n}\n\nconsole.log(\"Soma:\", soma);','1. Crie uma variável para armazenar a soma\n2. Use um loop for para percorrer o array\n3. Some cada número à variável soma\n4. Exiba o resultado','Use let soma = 0 para inicializar\nfor (let i = 0; i < array.length; i++) para loop\nsoma += numeros[i] para somar',1,'2025-09-08 17:27:41','2025-09-08 17:27:41'),(9,4,'Olá Mundo PHP','Exiba uma mensagem usando PHP','beginner','php','<?php\n// Escreva seu código aqui\n?>','<?php\necho \"Olá, Mundo!\";\n?>','1. Use echo para exibir a mensagem \"Olá, Mundo!\"','echo exibe texto em PHP\nUse aspas para strings',1,'2025-09-08 17:27:41','2025-09-08 17:27:41'),(10,4,'Variáveis e Concatenação','Trabalhe com variáveis e concatenação de strings','beginner','php','<?php\n$nome = \"João\";\n$idade = 25;\n// Exiba uma mensagem usando as variáveis\n?>','<?php\n$nome = \"João\";\n$idade = 25;\necho \"Olá, meu nome é \" . $nome . \" e tenho \" . $idade . \" anos.\";\n?>','1. Use as variáveis $nome e $idade\n2. Concatene as strings usando o operador .\n3. Crie uma frase apresentando a pessoa','Use . para concatenar strings em PHP\n$variavel acessa o valor da variável',1,'2025-09-08 17:27:41','2025-09-08 17:27:41'),(11,4,'Array e Loop','Trabalhe com arrays e estruturas de repetição','intermediate','php','<?php\n$frutas = [\"maçã\", \"banana\", \"laranja\", \"uva\"];\n// Exiba cada fruta em uma linha\n?>','<?php\n$frutas = [\"maçã\", \"banana\", \"laranja\", \"uva\"];\n\nforeach ($frutas as $fruta) {\n    echo $fruta . \"<br>\";\n}\n?>','1. Use foreach para percorrer o array\n2. Exiba cada fruta seguida de uma quebra de linha (<br>)\n','foreach ($array as $item) para percorrer\n<br> cria quebra de linha em HTML',1,'2025-09-08 17:27:41','2025-09-08 17:27:41'),(12,5,'Página Interativa','Crie uma página com HTML, CSS e JavaScript','advanced','mixed','<!DOCTYPE html>\n<html>\n<head>\n    <title>Página Interativa</title>\n    <style>\n        /* Adicione CSS aqui */\n    </style>\n</head>\n<body>\n    <h1>Contador</h1>\n    <p id=\"contador\">0</p>\n    <button onclick=\"incrementar()\">+</button>\n    <button onclick=\"decrementar()\">-</button>\n    \n    <script>\n        // Adicione JavaScript aqui\n    </script>\n</body>\n</html>','<!DOCTYPE html>\n<html>\n<head>\n    <title>Página Interativa</title>\n    <style>\n        body {\n            text-align: center;\n            font-family: Arial, sans-serif;\n        }\n        #contador {\n            font-size: 48px;\n            color: blue;\n            margin: 20px;\n        }\n        button {\n            font-size: 24px;\n            padding: 10px 20px;\n            margin: 5px;\n        }\n    </style>\n</head>\n<body>\n    <h1>Contador</h1>\n    <p id=\"contador\">0</p>\n    <button onclick=\"incrementar()\">+</button>\n    <button onclick=\"decrementar()\">-</button>\n    \n    <script>\n        let valor = 0;\n        \n        function incrementar() {\n            valor++;\n            document.getElementById(\"contador\").textContent = valor;\n        }\n        \n        function decrementar() {\n            valor--;\n            document.getElementById(\"contador\").textContent = valor;\n        }\n    </script>\n</body>\n</html>','1. Estilize a página com CSS (centralize, mude cores, tamanhos)\n2. Crie uma variável JavaScript para armazenar o valor\n3. Implemente as funções incrementar() e decrementar()\n4. Atualize o texto do elemento com id \"contador\"','Use document.getElementById() para acessar elementos\ntextContent altera o texto de um elemento\n++ incrementa, -- decrementa\nCSS: text-align: center para centralizar',1,'2025-09-08 17:27:41','2025-09-08 17:27:41');
/*!40000 ALTER TABLE `exercises` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-08 15:30:15
