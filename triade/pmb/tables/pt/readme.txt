-- +-------------------------------------------------+
-- © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
-- +-------------------------------------------------+
-- $Id: readme.txt,v 1.5 2021/05/03 10:13:12 dbellamy Exp $

------------------------------------------------------------------------------------------------------------------

Descrição dos arquivos
bibli.sql : estrutura da base de dados, sem dados

minimum.sql : utilizador admin/admin, parâmetros da aplicação

feed_essential.sql : o que se necessita para iniciar a aplicação em modo início rápido :
	Dados da aplicação preenchidos, modificáveis.
	Um conjunto de cópia de segurança pronto a empregar
	Um conjunto de parâmetros de Z3950.
	
data_test.sql : uma pequena selecção de dados de registos, utilizadores, para poder experimentar o PMB.
	Registos, utilizadores, empréstimos, exemplares, publicações periódicas
	Baseia-se nos dados da aplicação incluídos também em feed_essential.sql
	Deve-se carregar o thesaurus UNESCO_FR unesco_fr.sql
	
Tesauros : propõem-se 3 thesaurus :
	unesco_fr.sql : thesaurus hierárquico da UNESCO, bastante importante e bem construído.
	agneaux.sql : um poco mais pequeno, mais simples más também bem construído.
	environnement : um thesaurus útil para un fundo documental relacionado com o meio ambiente.
	
Indexations internes : propõem-se 4 indexações :
	indexint_100.sql : 100 casos do saber ou margarida de cores, indexação decimal 
	style Dewey simplificada para educação
	indexint_chambery.sql : indexação de estilo Dewey da BM de Chambéry, bem concebido mas pouco adaptada
	a bibliotecas pequenas
	indexint_dewey.sql : indexação estilo Dewey
	indexint_small_en.sql : indexação estilo Dewey reduzida e em inglês
	

************************************************************************************************
________________________________________________________________________________________________
Atenção, se está a fazer uma actualização de uma versão anterior :
------------------------------------------------------------------------------------------------
*********** A realizar a cada instalação ou actualização da aplicação  ****************
Quando instala uma nova versão
sobre uma versão anterior deve, obrigatoriamente,
antes de copiar os arquivos novos contidos no arquivo zip
no servidor web :

comprovar que os parâmetros incluídos em :
./includes/db_param.inc.php
./opac_css/includes/opac_db_param.inc.php

correspondem à sua configuração (faça uma cópia antes !)

Adicionalmente :
Deve fazer uma actualização da base de dados.
Não se perderá nada.

Ligue-se da forma habitual ao PMB, o estílo gráfico pode ser diferente, 
ausente (visualização sem cores nem imagens)

Vá a Administração > Ferramentas > act base de dados para actualizar a
base de dados.

Uma série de mensagens irão indicando as actualizações sucessivas, 
para continuar a actualização clique no link da parte inferior da página 
até que apareça a mensagem 'A sua base de dados está actualizada com a versão...'

Pode editar a sua conta de utilizador para modificar as suas preferências, mudando
o estilo de visualização.

Faça chegar as suas dúvidas, problemas ou sugestões por correio
electrónico : pmb@sigb.net

Por outro lado, gostaremos de contar consigo como um dos nossos
utilizadores, e se nos facilitar alguns dados como o número de utilizadores, de obras
de CD... junto com os dados do seu estabelecimento (ou a título particular) 
nos ajudará a conhecê-lo melhor.

Encontrará mais informação no directório ./doc ou 
na nossa página web http://www.sigb.net

A equipa de desenvolvimento.

