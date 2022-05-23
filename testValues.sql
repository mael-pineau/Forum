INSERT INTO user (id, mail, username, password, image, is_admin)
VALUES (1, "admin@gmail.com", "administrateur", "0b14d501a594442a01c6859541bcb3e8164d183d32937b851835442f69d5c94e", "default.png", true),
(2, "jean@gmail.com", "Jean Claude", "0b14d501a594442a01c6859541bcb3e8164d183d32937b851835442f69d5c94e", "default.png", false),
(3, "Jaque@gmail.com", "Jaque RacChi", "0b14d501a594442a01c6859541bcb3e8164d183d32937b851835442f69d5c94e", "default.png", false),
(4, "marine@gmail.com", "Marine Pomerleau ", "0b14d501a594442a01c6859541bcb3e8164d183d32937b851835442f69d5c94e", "default.png", false),
(5, "gauthier@gmail.com", "Gauthier Laframboise", "0b14d501a594442a01c6859541bcb3e8164d183d32937b851835442f69d5c94e", "default.png", false),
(6, "beluga@gmail.com", "Beluga", "0b14d501a594442a01c6859541bcb3e8164d183d32937b851835442f69d5c94e", "default.png", false),
(7, "coder@gmail.com", "The Coder", "0b14d501a594442a01c6859541bcb3e8164d183d32937b851835442f69d5c94e", "default.png", false),
(8, "hecker@gmail.com", "Hecker", "0b14d501a594442a01c6859541bcb3e8164d183d32937b851835442f69d5c94e", "default.png", false)

INSERT INTO subject (id, user_id, title, description, is_closed, date)
VALUES (1, 4, "Soumettre un formulaire HTML", "&lt;div&gt;Je pensais que l&#039;ajout d&#039;un attribut &quot;value&quot; d&eacute;fini sur l&#039;&eacute;l&eacute;ment &amp;lt;select&amp;gt; ci-dessous entra&icirc;nerait la s&eacute;lection par d&eacute;faut de l&#039;&amp;lt;option&amp;gt; contenant ma &quot;value&quot; fournie :&lt;br&gt;&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;pre&gt;&amp;lt;select name=&quot;hall&quot; id=&quot;hall&quot; value=&quot;3&quot;&amp;gt;
  &amp;lt;option&amp;gt;1&amp;lt;/option&amp;gt;
  &amp;lt;option&amp;gt;2&amp;lt;/option&amp;gt;
  &amp;lt;option&amp;gt;3&amp;lt;/option&amp;gt;
  &amp;lt;option&amp;gt;4&amp;lt;/option&amp;gt;
  &amp;lt;option&amp;gt;5&amp;lt;/option&amp;gt;
&amp;lt;/select&amp;gt;&lt;/pre&gt;&lt;div&gt;&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;Cependant, cela n&#039;a pas fonctionn&eacute; comme je l&#039;avais pr&eacute;vu.&lt;br&gt;&lt;strong&gt;Comment puis-je d&eacute;finir quel &eacute;l&eacute;ment &amp;lt;option&amp;gt; est s&eacute;lectionn&eacute; par d&eacute;faut ?&lt;/strong&gt;&lt;/div&gt;&lt;div&gt;&lt;br&gt;&lt;/div&gt;"
, true, UNIX_TIMESTAMP),

(2, 2, "Impossible de mettre un '.' dans un paramètre de route Symfony",
 "&lt;div&gt;J&#039;ai besoin de faire pass&eacute; une adresse email en tant que param&egrave;tre dans une URL d&#039;une de mes route Symfony, ce qui me retourne :&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;pre&gt; No route found for &quot;GET http://localhost:9000/user-profil...&quot;&lt;/pre&gt;&lt;div&gt;&lt;br&gt;Est-ce que quelqu&#039;un peut m&#039;aider sur ce probl&egrave;me?&amp;nbsp;&lt;br&gt;&lt;br&gt;&lt;/div&gt;"
 , true, UNIX_TIMESTAMP),


INSERT INTO comment (id, subject_id, user_id, message, date)
VALUES (1, 1, 7, "&lt;div&gt;D&eacute;fini&amp;nbsp;&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;pre&gt;selected=&quot;selected&quot; &lt;/pre&gt;&lt;div&gt;&lt;br&gt;pour l&#039;option que tu souhaite utiliser par d&eacute;faut.&lt;br&gt;&lt;br&gt;Example :&lt;br&gt;&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;pre&gt;&amp;lt;select name=&quot;hall&quot; id=&quot;hall&quot;&amp;gt;
  &amp;lt;option&amp;gt; 1 &amp;lt;/option&amp;gt;
  &amp;lt;option selected=&quot;selected&quot; &amp;gt; 2 &amp;lt;/option&amp;gt;
  &amp;lt;option&amp;gt; 3 &amp;lt;/option&amp;gt;
  &amp;lt;option&amp;gt; 4 &amp;lt;/option&amp;gt;
  &amp;lt;option&amp;gt; 5 &amp;lt;/option&amp;gt;
&amp;lt;/select&amp;gt;&lt;/pre&gt;&lt;div&gt;Dans ce cas la l&#039;option 2 sera celle qui sera choisi par d&eacute;faut&lt;/div&gt;&lt;div&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;&lt;br&gt;&lt;/div&gt;"
, UNIX_TIMESTAMP)