<?php
/** 
 * As configurações básicas do WordPress.
 *
 * Esse arquivo contém as seguintes configurações: configurações de MySQL, Prefixo de Tabelas,
 * Chaves secretas, Idioma do WordPress, e ABSPATH. Você pode encontrar mais informações
 * visitando {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. Você pode obter as configurações de MySQL de seu servidor de hospedagem.
 *
 * Esse arquivo é usado pelo script ed criação wp-config.php durante a
 * instalação. Você não precisa usar o site, você pode apenas salvar esse arquivo
 * como "wp-config.php" e preencher os valores.
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar essas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'devloc');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'webmaster');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', 'Cdilan10');

/** nome do host do MySQL */
define('DB_HOST', 'localhost');

/** Conjunto de caracteres do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O tipo de collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer cookies existentes. Isto irá forçar todos os usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'e`,Hq$;,NR!+w({CWyb/fho|8/fDwy-:;T].#/D3tnlLn-x+3Mx-DZ&F]+EsX90V');
define('SECURE_AUTH_KEY',  '1|_BM6(n2r}2vD@Jbp|m&!_Oi~@!k>p|^)4a}Tt5=V@s6H-7E81~sp5EU-B_cEEa');
define('LOGGED_IN_KEY',    'Bqjdxf,}=S2+BNc127Mi;37T|]x82Euy-_c<#TD|O~~<;3G)rGAHPC&~KV>mHiz~');
define('NONCE_KEY',        '8{rVn^{|[}JSkH>R~k&u8s>!t*@K</|X^xI4hpg>d7Jx},Q845Qn*++JGX32Yp!a');
define('AUTH_SALT',        '<v-<u7KFna#)~huu@N@+o#;Zk<glZ`hl&E_}W)dLdE;Bp$ZGXBN/n.+yVwD:nIay');
define('SECURE_AUTH_SALT', '/8fC--g+NxB-LKk;b#Ql3Ih_Q}m:LyC-aH4n=J<Q+=kvwugI+D|1#FiJm!@0,Wt4');
define('LOGGED_IN_SALT',   '~e6+Btk0a<Fzh`GHHQ=EKc[p:0/(2BPKg+PXUGuT|yN7O$~NihC0GA5-<M7u!+s!');
define('NONCE_SALT',       'cAJ|kIWhP|f-F0]DO2M!RuXG 0yEkr0;f7OeJ?s2I$#>nDI@4x]do8JYLY]0Z.Yr');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der para cada um um único
 * prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * O idioma localizado do WordPress é o inglês por padrão.
 *
 * Altere esta definição para localizar o WordPress. Um arquivo MO correspondente ao
 * idioma escolhido deve ser instalado em wp-content/languages. Por exemplo, instale
 * pt_BR.mo em wp-content/languages e altere WPLANG para 'pt_BR' para habilitar o suporte
 * ao português do Brasil.
 */
define('WPLANG', 'pt_BR');
define('WP_MEMORY_LIMIT', '128M');
/**
 * Para desenvolvedores: Modo debugging WordPress.
 *
 * altere isto para true para ativar a exibição de avisos durante o desenvolvimento.
 * é altamente recomendável que os desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
/** Configura as variáveis do WordPress e arquivos inclusos. */
require_once(ABSPATH . 'wp-settings.php');
