CREATE DATABASE IF NOT EXISTS `bd_emprestimos_ti` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bd_emprestimos_ti`;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `devolucao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devolucao` (
  `id_emprest` int(6) NOT NULL,
  `data_devolucao` date NOT NULL,
  `condicoes_devolucao` varchar(100) DEFAULT NULL,
  `id_tecnico_recebimento` int(6) NOT NULL,
  `status_equip` enum('Disponível','Emprestado','Em manutenção','Obsoleto/descartado') NOT NULL,
  PRIMARY KEY (`id_emprest`),
  KEY `fk_id_tecnico_recebimento` (`id_tecnico_recebimento`),
  CONSTRAINT `fk_emprest` FOREIGN KEY (`id_emprest`) REFERENCES `emprestimo` (`id_emprest`),
  CONSTRAINT `fk_id_tecnico_recebimento` FOREIGN KEY (`id_tecnico_recebimento`) REFERENCES `funcionario` (`id_func`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `devolucao` WRITE;
/*!40000 ALTER TABLE `devolucao` DISABLE KEYS */;
INSERT INTO `devolucao` VALUES (1,'2026-05-20','bom estado, pequenos riscos na tampa',1002,'Disponível'),(2,'2026-07-15','tela trincada no canto inferior',1001,'Em manutenção'),(3,'2026-04-30','não liga após queda, placa danificada',1001,'Obsoleto/descartado'),(4,'2026-08-28','teclado com teclas falhando',1002,'Em manutenção'),(5,'2026-03-12','funcionando, lâmpada ok',1003,'Disponível'),(6,'2026-09-29','bom estado, com capa',1003,'Disponível'),(7,'2026-08-10','fone esquerdo sem som',1001,'Em manutenção'),(8,'2026-10-20','bom estado',1002,'Disponível'),(10,'2026-09-01','bateria não segura carga',1003,'Em manutenção');
/*!40000 ALTER TABLE `devolucao` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `emprestimo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emprestimo` (
  `id_emprest` int(6) NOT NULL AUTO_INCREMENT,
  `id_func` int(6) NOT NULL,
  `id_equip` int(15) NOT NULL,
  `data_retirada` date NOT NULL,
  `previsao_devolucao` date DEFAULT NULL,
  `id_tecnico_entrega` int(6) NOT NULL,
  `condicao_retirada` varchar(100) NOT NULL,
  PRIMARY KEY (`id_emprest`),
  KEY `fk_equipamento` (`id_equip`),
  KEY `fk_tecnico` (`id_tecnico_entrega`),
  KEY `fk_funcionario` (`id_func`) USING BTREE,
  CONSTRAINT `fk_equipamento` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`),
  CONSTRAINT `fk_funcionario` FOREIGN KEY (`id_func`) REFERENCES `funcionario` (`id_func`),
  CONSTRAINT `fk_tecnico` FOREIGN KEY (`id_tecnico_entrega`) REFERENCES `funcionario` (`id_func`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `emprestimo` WRITE;
/*!40000 ALTER TABLE `emprestimo` DISABLE KEYS */;
INSERT INTO `emprestimo` VALUES (1,1012,2003,'2026-01-12','2026-06-30',1004,'novo, na caixa'),(2,1005,2009,'2026-02-03','2026-08-03',1002,'novo, com capinha'),(3,1014,2010,'2026-02-20','2026-12-20',1001,'novo, na caixa'),(4,1006,2001,'2026-03-02','2026-09-02',1003,'novo, na caixa'),(5,1007,2016,'2026-03-10','2026-03-12',1003,'funcionando, com controle e cabo HDMI'),(6,1008,2012,'2026-04-06','2026-10-06',1002,'novo, com capa'),(7,1013,2015,'2026-05-04','2026-11-04',1001,'novo'),(8,1009,2006,'2026-06-01','2026-12-01',1003,'novo, na caixa'),(9,1007,2003,'2026-06-01','2026-11-10',1001,'bom estado, pequenos riscos na tampa'),(10,1011,2005,'2026-06-15','2026-12-15',1002,'bom estado'),(11,1005,2002,'2026-07-20','2027-01-20',1001,'bom estado'),(12,1006,2008,'2026-08-01','2026-10-31',1003,'novo, com capinha e película'),(13,1010,2001,'2026-09-15','2026-12-15',1002,'teclado substituído, bom estado'),(14,1008,2011,'2026-10-01','2026-10-15',1002,'bom estado, com carregador'),(15,1013,2014,'2026-10-26','2026-11-03',1003,'bom estado'),(16,1009,2018,'2026-11-02','2026-11-12',1002,'nova, com tampa de privacidade');
/*!40000 ALTER TABLE `emprestimo` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `equipamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipamento` (
  `id_equip` int(15) NOT NULL,
  `numero_serie` varchar(15) NOT NULL,
  `tipo` varchar(15) NOT NULL,
  `marca_modelo` varchar(20) NOT NULL,
  `condicoes_atual` varchar(100) NOT NULL,
  `status_equip` enum('Disponível','Emprestado','Em manutenção','Obsoleto/descartado') NOT NULL,
  PRIMARY KEY (`id_equip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `equipamento` WRITE;
/*!40000 ALTER TABLE `equipamento` DISABLE KEYS */;
INSERT INTO `equipamento` VALUES (2001,'DL5440-A01','Notebook','Dell Latitude 5440','teclado substituído, bom estado','Emprestado'),(2002,'DL5440-A02','Notebook','Dell Latitude 5440','bom estado','Emprestado'),(2003,'TPE14-0031','Notebook','Lenovo ThinkPad E14','bom estado, pequenos riscos na tampa','Emprestado'),(2004,'TPE14-0032','Notebook','Lenovo ThinkPad E14','novo, na caixa','Disponível'),(2005,'HP450-7781','Notebook','HP ProBook 450','bateria não segura carga','Em manutenção'),(2006,'LG24-55102','Monitor','LG 24MK430H','bom estado','Disponível'),(2007,'SS27-90214','Monitor','Samsung S27R350','novo, na caixa','Disponível'),(2008,'GA54-33017','Celular','Samsung Galaxy A54','novo, com capinha e película','Emprestado'),(2009,'IP13-48820','Celular','Apple iPhone 13','tela trincada no canto inferior','Em manutenção'),(2010,'MG84-12093','Celular','Motorola Moto G84','não liga após queda, placa danificada','Obsoleto/descartado'),(2011,'TA9-66410','Tablet','Galaxy Tab A9','bom estado, com carregador','Emprestado'),(2012,'IPAD9-2207','Tablet','Apple iPad 9','bom estado, com capa','Disponível'),(2013,'M280-0145','Mouse','Logitech M280','novo','Disponível'),(2014,'K120-0388','Teclado','Logitech K120','bom estado','Emprestado'),(2015,'JBLQ1-5521','Headset','JBL Quantum 100','sem conserto, fone esquerdo queimado','Obsoleto/descartado'),(2016,'EPE20-1190','Projetor','Epson PowerLite E20','funcionando, lâmpada ok','Disponível'),(2017,'POSN-00412','Notebook','Positivo Master N','não liga, modelo antigo sem peças','Obsoleto/descartado'),(2018,'C920-7730','Webcam','Logitech C920','nova, com tampa de privacidade','Emprestado');
/*!40000 ALTER TABLE `equipamento` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `funcionario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `funcionario` (
  `id_func` int(6) NOT NULL,
  `nome_completo` varchar(50) NOT NULL,
  `email_corporativo` varchar(30) NOT NULL,
  `departamento` enum('Administrativo','Comercial','Financeiro','Jurídico','Logística','Marketing','RH','Suporte','TI') NOT NULL,
  `cargo` varchar(20) NOT NULL,
  `status_func` enum('Ativo','Afastado','Desligado') NOT NULL,
  PRIMARY KEY (`id_func`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `funcionario` WRITE;
/*!40000 ALTER TABLE `funcionario` DISABLE KEYS */;
INSERT INTO `funcionario` VALUES (1001,'Carlos Eduardo Lima','carlos.lima@empresa.com','TI','Analista de Suporte','Ativo'),(1002,'Fernanda Souza Rocha','fernanda.rocha@empresa.com','TI','Técnica de TI','Ativo'),(1003,'Ricardo Alves Pinto','ricardo.pinto@empresa.com','TI','Coordenador de TI','Ativo'),(1004,'Juliana Martins','juliana.martins@empresa.com','TI','Técnica de TI','Afastado'),(1005,'Mariana Costa Ribeiro','mariana.ribeiro@empresa.com','Comercial','Vendedora','Ativo'),(1006,'Pedro Henrique Santos','pedro.santos@empresa.com','Financeiro','Analista Financeiro','Ativo'),(1007,'Ana Paula Ferreira','ana.ferreira@empresa.com','RH','Analista de RH','Ativo'),(1008,'Lucas Oliveira Gomes','lucas.gomes@empresa.com','Marketing','Designer','Ativo'),(1009,'Beatriz Carvalho','beatriz.carvalho@empresa.com','Jurídico','Advogada','Ativo'),(1010,'Rafael Mendes Araújo','rafael.araujo@empresa.com','Logística','Supervisor','Ativo'),(1011,'Camila Duarte Freitas','camila.freitas@empresa.com','Administrativo','Assistente Adm.','Afastado'),(1012,'Gustavo Pereira Nunes','gustavo.nunes@empresa.com','Comercial','Gerente Comercial','Desligado'),(1013,'Larissa Teixeira','larissa.teixeira@empresa.com','Suporte','Atendente','Ativo'),(1014,'Thiago Barbosa','thiago.barbosa@empresa.com','Financeiro','Contador','Desligado');
/*!40000 ALTER TABLE `funcionario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

