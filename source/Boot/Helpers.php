<?php

/**
 * ####################
 * ###   VALIDATE   ###
 * ####################
 */

use Source\Models\Settings\Numerador;
use Source\Models\Users\Auth;

/**
 * Valida se uma string é um e-mail válido.
 *
 * @param string $email
 * @return bool true se o e-mail for válido, false caso contrário
 */
function is_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
/**
 * Valida um número de telefone com 9 dígitos.
 * Aceita string ou inteiro, ignorando caracteres não numéricos.
 *
 * @param string|int $fone
 * @return bool
 */
function is_fone(string|int $fone): bool
{
    // Mantém apenas dígitos
    $digits = preg_replace('/\D+/', '', (string) $fone);

    return (bool) preg_match('/^[0-9]{9}$/', $digits);
}
/**
 * @param string $password
 * @return bool
 */
function is_passwd(string $password): bool
{
    if (password_get_info($password)['algo'] || (mb_strlen($password) >= CONF_PASSWD_MIN_LEN && mb_strlen($password) <= CONF_PASSWD_MAX_LEN)) {
        return true;
    }

    return false;
}

/**
 * ##################
 * ###   STRING   ###
 * ##################
 */

/**
 * @param string $string
 * @return string
 */
function str_slug(string $string): string
{
    $string = filter_var(mb_strtolower($string), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $formats = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
    $replace = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';

    $slug = str_replace(
        ["-----", "----", "---", "--"],
        "-",
        str_replace(
            " ",
            "-",
            trim(strtr(utf8_decode($string), utf8_decode($formats), $replace))
        )
    );
    return $slug;
}

/**
 * Mostrar array dias da semana
 *
 * @return array
 */
function weekly_array(): array
{
    return ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
}

/**
 * @param string $string
 * @return string
 */
function str_studly_case(string $string): string
{
    $string = str_slug($string);
    $studlyCase = str_replace(
        " ",
        "",
        mb_convert_case(str_replace("-", " ", $string), MB_CASE_TITLE)
    );

    return $studlyCase;
}
/**
 * type_product_array
 *
 * @return array
 */
function type_product_array(): array
{
    return [1 => 'Natural', 2 => 'Convencional', 3 => 'Alimentar', 4 => 'Diverso'];
}
/**
 * @param string $string
 * @return string
 */
function str_camel_case(string $string): string
{
    return lcfirst(str_studly_case($string));
}

/**
 * emprestimos por funcionário
 *
 * @return array
 */
function emprest_array(): array
{
    return [1 => 'Adiatamento salárial', 2 => 'Consulta ou tratamento', 3 => 'Medicamentos', 4 => 'Definir tipo de emprestimo'];
}


/**
 * saletype_array_map
 *
 * @return array
 */
function saletype_array_map(): array
{
    return [1 => 'Vendas por categoria', 2 => 'Vendas por Produto', 3 => 'Vendas por Funcionário', 4 => 'Pontos de vendas', 5 => 'Saída por categoria', 6 => 'Saída área ou necessidade'];
}
/**
 * Converte uma string para "Title Case" respeitando caracteres especiais.
 *
 * @param string|null $string
 * @return string|null
 */
function str_title(?string $string = null): ?string
{
    return (!empty($string) ? mb_convert_case(filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS), MB_CASE_TITLE) : '');
}
/**
 * @param string $string
 * @return string
 */
function str_title_upcase(string $string): string
{
    return mb_convert_case(filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS), MB_CASE_UPPER);
}
/**
 * Periodo de aulas *
 * @return array
 */
function period_array(): array
{
    return [1 => "Manhã", 2 => "Tarde", 3 => "Noite"];
}

/**
 * Vinculo laboral
 * @return array
 */
function vinculo_laboral_array(): array
{
    return [
        1 => "Contrato de Termo Certo",
        2 => "Contrato de Titulo de Provimento",
        3 => "Contrato de Titulo de Quadro",
        4 => "Contrato de Tempo Determinado",
        5 => "Contrato de Trabalho Especiais",
        6 => "Contrato de Titulo de Estangeiro Não Residentes"
    ];
}
/**
 * @param string $text
 * @return string
 */
function str_textarea(string $text): string
{
    $text = filter_var($text, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $arrayReplace = ["&#10;", "&#10;&#10;", "&#10;&#10;&#10;", "&#10;&#10;&#10;&#10;", "&#10;&#10;&#10;&#10;&#10;"];
    return "<p>" . str_replace($arrayReplace, "</p><p>", $text) . "</p>";
}

/**
 * Regime da trabalho por funcionário array
 *
 * @return array
 */
function Work_regime_array(): array
{
    return [
        10 => "Parcial",
        20 => "Integral"
    ];
}

/**
 * Array área de formação da cadeira.
 *
 * @return array
 */
function formaction_type_array(): array
{
    return [0 => "Não definida", 1 => 'Sociocultural', 2 => 'Científica', 3 => 'Técnica, Tecnológica e Prática', 4 => 'Específica', 5 => 'Complementar', 6 => 'Geral'];
}




/**
 * @param string $string
 * @param int $limit
 * @param string $pointer
 * @return string
 */
function str_limit_words(string $string, int $limit, string $pointer = "..."): string
{
    $string = trim(filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS));
    $arrWords = explode(" ", $string);
    $numWords = count($arrWords);

    if ($numWords < $limit) {
        return $string;
    }

    $words = implode(" ", array_slice($arrWords, 0, $limit));
    return "{$words}{$pointer}";
}

/**
 * @param string $string
 * @param int $limit
 * @param string $pointer
 * @return string
 */
function str_limit_chars(string $string, int $limit, string $pointer = "..."): string
{
    $string = trim(filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS));
    if (mb_strlen($string) <= $limit) {
        return $string;
    }

    $chars = mb_substr($string, 0, mb_strrpos(mb_substr($string, 0, $limit), " "));
    return "{$chars}{$pointer}";
}

/**
 * @param string $price
 * @return string
 */
function str_price(?string $price): string
{
    return number_format((!empty($price) ? $price : 0), 2, ",", ".");
}

/**
 * @param  mixed $media
 * @return string
 */
function formatar_media(string $media): string
{
    if (!empty($media)) {
        if (strstr($media, '.')) :
            return number_format($media, 0, ',', '');
        else :
            return "$media";
        endif;
    } else {
        return "$media";
    }
}
/**
 * Retornar array de genero
 * @return array
 */
function sexo_array(): array
{
    return  ["M" => "Masculino", "F" => "Femenino"];
}

/**
 * Plano semestres array
 * @return array
 */
function semestres_array(): array
{
    return [1 => "I Semestre", 2 => "II Semestre", 3 => "Anual"];
}


/**
 * Necessidade de educação especial *
 * @return array
 */
function educa_especial_array(?string $sexo = null): array
{
    $sexo = ($sexo == 'F' ? 'a' : 'o');
    return ['1' => "Ceg{$sexo}", '2' => "Surd{$sexo}", '3' => 'Fisico motor', '4' => 'Sem necessidade de Educação Especial', '5' => 'Outro'];
}



/**
 * Mandar um array de meios de pagamentos *
 * @return array
 */
function metod_pay_array(): array
{
    return [6 => "Em cash", 2 => "Cartão multicaixa", 3 => "Transferência bancária", 4 => "Depósito bancário", 5 => "Ordem de saque"];
}

/**
 * Habilitações académicas para funcionário
 * array geral
 * @return array
 */
function Literary_abilities_array(): array
{
    return [
        1 => "Não Alfabetizado",
        2 => "Ensino Primário",
        3 => "Ensino Secundário do I Ciclo",
        4 => "Ensino Secundário do II Ciclo",
        5 => "Técnico Médio",
        6 => "Frequência Universitária",
        7 => "Bacharel",
        8 => "Licenciado",
        9 => "Mestre",
        10 => "Doutor"
    ];
}


/**
 * Estado de conservação *
 * @return array
 */
function status_array(): array
{
    return [1 => "Bom", 2 => "Regular", 3 => "Mau"];
}

/**
 * Texto de felicitação aos aniversariantes
 *
 * @param  string $Nome
 * @return string
 */
function congratulate(string $Nome): string
{
    $votos = 'Dr. José Nguepe';
    $mensagens = [
        1  => "{$Nome}, feliz aniversário!\nCurta cada instante deste dia e aproveite a vida ao máximo.\nVotos {$votos}.",
        2  => "{$Nome}, para hoje desejamos alegria e para toda a sua vida muita saúde, amor e sucesso.\nVotos {$votos}.",
        3  => "Parabéns, {$Nome}! Esperamos que o seu dia seja inesquecível e que seus desejos se realizem na totalidade.\nVotos {$votos}.",
        4  => "{$Nome}, neste dia especial desejamos a você toda a felicidade do mundo.\nFeliz aniversário!\nVotos {$votos}.",
        5  => "{$Nome}, sorria muito hoje, pois o seu sorriso aquece corações como nada mais neste mundo. Seja muito feliz hoje e sempre!",
        6  => "Feliz aniversário, {$Nome}! Que o seu dia seja iluminado pela luz do amor e da paz, e que você receba muito carinho e boas surpresas.",
        7  => "{$Nome}, aproveite cada instante da sua vida e, hoje, desfrute de todos os momentos deste dia especial. Parabéns e muitas felicidades!",
        8  => "Feliz aniversário, {$Nome}! Que Deus lhe conceda muitos anos de vida com saúde, paz e alegria. Que você sinta sempre gratidão por todas as bênçãos.\nVotos {$votos}.",
        9  => "{$Nome}, que a sua vida seja sempre um caminho repleto de felicidade, e que nunca lhe falte amor, saúde e sucesso. Parabéns!",
        10 => "Feliz aniversário, {$Nome}! Que este dia fique marcado na sua memória pelas melhores razões.\nDesejamos amor, paz, saúde e muita alegria para comemorar esta data em grande estilo.",
        11 => "Parabéns, {$Nome}! Que hoje se inicie mais um ciclo de conquistas, aprendizados e vitórias na sua vida.",
        12 => "{$Nome}, que Deus abençoe cada passo seu e que você continue sendo motivo de orgulho para todos nós. Feliz aniversário!",
        13 => "Feliz aniversário, {$Nome}! Que nunca falte fé, esperança e coragem para realizar os seus sonhos.",
        14 => "Hoje é dia de celebrar a sua vida, {$Nome}. Que você seja sempre rodeado de pessoas que te amam e te querem bem.",
        15 => "Parabéns, {$Nome}! Que o seu coração seja sempre morada de paz, amor e gratidão.",
        16 => "{$Nome}, desejamos que cada novo ano traga mais sabedoria, maturidade e realizações. Feliz aniversário!",
        17 => "Feliz aniversário, {$Nome}! Que você tenha muitos motivos para sorrir hoje e em todos os dias que virão.",
        18 => "{$Nome}, que o brilho deste dia se prolongue por todo o ano e que você realize todos os seus projetos. Parabéns!",
        19 => "Parabéns pelo seu dia, {$Nome}! Que a felicidade caminhe sempre ao seu lado.",
        20 => "{$Nome}, celebre a vida, os amigos e a família. Que este aniversário seja apenas o começo de um tempo maravilhoso."
    ];

    return $mensagens[array_rand($mensagens)];
}

/**
 * Tipo de material para salas *
 * @return array
 */
function type_material_array(): array
{
    return [1 => "Aluminio vidrado", 2 => "Aluninio simples", 3 => "Ferro vidrado", 4 => "Ferro Simples", 5 => "Madeira vidrada", 6 => "Madeira", 7 => "Enox"];
}
/**
 * Estaddo civil
 *
 * @return array
 */
function statd_civil_array(?string $sexo = null): array
{
    $sexo =  ($sexo == 'F' ? 'a' : 'o');
    return ['1' => 'Solteir' . $sexo, '2' => 'Casad' . $sexo, '3' => 'Divorciad' . $sexo, '4' => 'Viúv' . $sexo];
}

/**
 * ###############
 * ###   URL   ###
 * ###############
 */

/**
 * @param string $path
 * @return string
 */
function url(?string $path = null): string
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $isLocalHost = in_array($host, ['www.localhost', 'localhost', '127.0.0.1', 'cvn.local', 'www.cvn.local'], true)
        || str_ends_with($host, '.local');

    if ($isLocalHost) {
        if ($path) {
            return CONF_URL_TEST . "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
        }
        return CONF_URL_TEST;
    }

    if ($path) {
        return CONF_URL_BASE . "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
    }

    return CONF_URL_BASE;
}

/**
 * @return string
 */
function url_back(): string
{
    return ($_SERVER['HTTP_REFERER'] ?? url());
}
/**
 * @param string $url
 */
function redirect(string $url): void
{
    header("HTTP/1.1 302 Redirect");
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        header("Location: {$url}");
        exit;
    }
    if (filter_input(INPUT_GET, "route", FILTER_DEFAULT) != $url) {
        $location = url($url);
        header("Location: {$location}");
        exit;
    }
}

/**
 * ##################
 * ###   ASSETS   ###
 * ##################
 */

/**
 * @return \Source\Models\User|null
 */
function user(): ?\Source\Models\Users\User
{
    return \Source\Models\Users\Auth::user();
}

/**
 * @return \Source\Core\Session
 */
function session(): \Source\Core\Session
{
    return new \Source\Core\Session();
}
function myNumber(int $type, int $instit): int
{
    $GFD = (new Numerador())->myNumber($type, $instit);
    return $GFD;
}


/**
 * Meses array *
 * @return array
 */
function meses_array(): array
{
    return [
        1 => "Janeiro",
        2 => "Fevereiro",
        3 => "Março",
        4 => "Abril",
        5 => "Maio",
        6 => "Junho",
        7 => "Julho",
        8 => "Agosto",
        9 => "Setembro",
        10 => "Outubro",
        11 => "Novembro",
        12 => "Dezembro"
    ];
}



/**
 * Data por extenção mostrar
 * @param  mixed $date
 * @return string
 */
function mounth_extenso(string $date): ?string
{
    $xs = meses_array();
    $sb = date('m', strtotime($date));
    return date('d', strtotime($date)) . ' de ' . $xs[intval($sb)] . ' de ' . date('Y', strtotime($date));
}
/**
 * @param  mixed $val
 * @param  mixed $taxa
 * @return null|string
 */
function porcentagem($val, $taxa = null): ?string
{
    if ($taxa) {
        return (($taxa / 100) * $val);
    }
    return null;
}

/**
 * @param  mixed $val1
 * @param  mixed $val2
 * @return null|string
 */
function str_percent2($val1 = null, $val2 = null, $decima = 0): ?string
{
    if ($val1 > 0 && $val2 > 0) {
        return number_format((($val2 / $val1) * 100), $decima) . '%';
    }
    return null;
}

/**
 * Retorna figura de homem ou mulher
 * @param  mixed $sex
 * @return string
 */
function iconPerson(string $sex): string
{
    if ($sex == 'M') {
        return '<i class="fas fa-male" style="margin-right: 8px; font-size:27px;"></i>';
    } else {
        return '<i class="fas fa-female" style="margin-right: 8px; font-size:27px;color:red;"></i>';
    }
}
/**
 * @param string|null $path
 * @param string $theme
 * @return string
 */
function theme(string $path = null): string
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $isLocalHost = in_array($host, ['www.localhost', 'localhost', '127.0.0.1', 'cvn.local', 'www.cvn.local'], true)
        || str_ends_with($host, '.local');

    $baseUrl = $isLocalHost ? CONF_URL_TEST : CONF_URL_BASE;

    if (!$path) {
        return $baseUrl . "/themes/";
    }

    $normalizedPath = ($path[0] == "/" ? mb_substr($path, 1) : $path);
    $assetUrl = $baseUrl . "/themes/" . $normalizedPath;
    $assetFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "themes" . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $normalizedPath);

    if (is_file($assetFile)) {
        return $assetUrl . "?v=" . filemtime($assetFile);
    }

    return $assetUrl;
}


/**
 * @param  mixed $area
 * @param  mixed $balc
 * @param  mixed $valor
 * @return array
 */
function audioArray($area, $balc, $valor = 0): array
{
    $passArr = [];
    $c = array("", "100.", "200.", "300.", "400.", "500.", "600.", "700.", "800.", "900.");
    $d = array("", "10.", "20.", "30.", "40.", "50.", "60.", "70.", "80.", "90.");
    $d10 = array("10.", "11.", "12.", "13.", "14.", "15.", "16.", "17.", "18.", "19.");
    $u = array("", "1.", "2.", "3.", "4.", "5.", "6.", "7.", "8.", "9.");
    $z = 0;
    $rt = '';
    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    for ($i = 0; $i < count($inteiro); $i++) {
        for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++) {
            $inteiro[$i] = "0" . $inteiro[$i];
        }
    }

    // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
    $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
    for ($i = 0; $i < count($inteiro); $i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento." : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

        $r = $rc . (($rc && ($rd || $ru)) ? " e. " : "") . $rd . (($rd && $ru) ? " e. " : "") . $ru;
        $t = count($inteiro) - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? '' : '') : "";
        if ($valor == "000") {
            $z++;
        } elseif ($z > 0) {
            $z--;
        }
        if (($t == 1) && ($z > 0) && ($inteiro[0] > 0)) {
            $r .= (($z > 1) ? " de " : "") . '';
        }
        if ($r) {
            $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e. ") : " ") . $r;
        }
    }
    if ($rt) {
        $passArr[$area] = array_merge(["chamada", $balc, "senha"], explode(".", $rt));
    }
    return $passArr;
}

/*
 * @param string $image
 * @param int $width
 * @param int|null $height
 * @return string
 */
function image(?string $image, int $width, ?int $height = null, $sexo = null): ?string
{

    if ($image) {
        // return url() . "/" . (new \Source\Support\Thumb())->make($image, $width, $height);
        return url("/storage/$image");
    }
    return theme(($sexo == 'M' ? '/assets/images/admin.png' : ($sexo == 'F' ? '/assets/images/femeninoIcon.png' : '/assets/images/noimage.jpg')));
}



/**
 * ################
 * ###   DATE   ###
 * ################
 */

/**
 * @param string $date
 * @param string $format
 * @return string
 * @throws Exception
 */
function date_fmt(?string $date, string $format = "d/m/Y H\hi"): string
{
    $date = (empty($date) ? "now" : $date);
    return (new DateTime($date))->format($format);
}

/**
 * @param string $date1
 * @return string
 * @throws Exception
 */
function date_diferenc(?string $date1): string
{
    $date1 = $date1 ?? "now";
    $interval = (new DateTime($date1))->diff(new DateTime("now"));

    // Retorna a diferença em dias com sinal (+/-), por exemplo "+10" ou "-3"
    return $interval->format('%R%a');
}

/**
 * @param string $date
 * @return string
 * @throws Exception
 */
function date_fmt_br(?string $date): string
{
    $date = (empty($date) ? "now" : $date);
    return (new DateTime($date))->format(CONF_DATE_AO);
}

/**
 * @param string $date
 * @return string
 * @throws Exception
 */
function date_fmt_app(?string $date): string
{
    $date = (empty($date) ? "now" : $date);
    return (new DateTime($date))->format(CONF_DATE_APP);
}
/**
 * Calcula idade em anos completos a partir de uma data (Y-m-d).
 *
 * @param string $nascimento
 * @return int|null
 */
function calculaidade(string $nascimento): ?int
{
    if (!$nascimento) {
        return null;
    }

    try {
        $birth = new DateTime($nascimento);
        $today = new DateTime('today');
        return (int) $birth->diff($today)->y;
    } catch (Exception $e) {
        return null;
    }
}


/**
 * Data formatada reverso
 *
 * @param  mixed $date
 * @return string
 */
function date_fmt_back(?string $date): ?string
{
    if (!$date) {
        return null;
    }
    //se tiver data e hora
    if (strpos($date, " ")) {
        $date = explode(" ", $date);
        return implode("-", array_reverse(explode("/", $date[0]))) . " " . $date[1];
    }

    return implode("-", array_reverse(explode("/", $date)));
}

/**
 * Escapa uma string para saída em HTML (atalho para htmlspecialchars).
 *
 * @param string|null $value
 * @return string
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
/**
 * ####################
 * ###   PASSWORD   ###
 * ####################
 */

/**
 * @param string $password
 * @return string
 */
function passwd(string $password): string
{
    if (!empty(password_get_info($password)['algo'])) {
        return $password;
    }

    return password_hash($password, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
}

/**
 * @param string $password
 * @param string $hash
 * @return bool
 */
function passwd_verify(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * @param string $hash
 * @return bool
 */
function passwd_rehash(string $hash): bool
{
    return password_needs_rehash($hash, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
}

/**
 * ###################
 * ###   REQUEST   ###
 * ###################
 */

/**
 * @return string
 */
function csrf_input(): string
{
    $session = new \Source\Core\Session();
    $session->csrf();
    return "<input type='hidden' name='csrf' value='" . ($session->csrf_token ?? "") . "'/>";
}

/**
 * @return string
 */
function csrf_string(): string
{
    $session = new \Source\Core\Session();
    $session->csrf();
    return ($session->csrf_token ?? "");
}

/**
 * @param $request
 * @return bool
 */
function csrf_verify($request): bool
{

    $session = new \Source\Core\Session();
    if (empty($session->csrf_token) || empty($request['csrf']) || $request['csrf'] != $session->csrf_token) {
        return false;
    }
    return true;
}

/**
 * @return null|string
 */
function flash(): ?string
{
    $session = new \Source\Core\Session();
    if ($flash = $session->flash()) {
        return $flash;
    }
    return null;
}

/**
 * @return null|array
 */
function flashJson(): ?array
{
    $session = new \Source\Core\Session();
    if ($flash = $session->flashJson()) {
        return $flash;
    }
    return null;
}

/**
 * @return \Source\Models\Users\User|null
 */
function auth_user(): ?\Source\Models\Users\User
{
    return Auth::user();
}

/**
 * @param string $permission
 * @param bool $allowWhenUnassigned
 * @return bool
 */
function can(string $permission, bool $allowWhenUnassigned = true): bool
{
    $user = auth_user();
    return $user ? $user->can($permission, $allowWhenUnassigned) : false;
}

/**
 * @param array $permissions
 * @param bool $allowWhenUnassigned
 * @return bool
 */
function can_any(array $permissions, bool $allowWhenUnassigned = true): bool
{
    $user = auth_user();
    return $user ? $user->canAny($permissions, $allowWhenUnassigned) : false;
}

/**
 * @param string $role
 * @param bool $allowWhenUnassigned
 * @return bool
 */
function has_role(string $role, bool $allowWhenUnassigned = true): bool
{
    $user = auth_user();
    return $user ? $user->hasRole($role, $allowWhenUnassigned) : false;
}
/**
 * @param string $key
 * @param int $limit
 * @param int $seconds
 * @return bool
 */
function request_limit(string $key, int $limit = 5, int $seconds = 60): bool
{
    $session = new \Source\Core\Session();
    if (!empty($session->$key->time) && $session->has($key) && $session->$key->time >= time() && !empty($session->$key->requests) && $session->$key->requests < $limit) {
        $session->set($key, [
            "time" => time() + $seconds,
            "requests" => $session->$key->requests + 1
        ]);
        return false;
    }

    if (!empty($session->$key->time) && $session->has($key) && $session->$key->time >= time() && !empty($session->$key->requests) &&  $session->$key->requests >= $limit) {
        return true;
    }

    $session->set($key, [
        "time" => time() + $seconds,
        "requests" => 1
    ]);

    return false;
}

/**
 * Limpa o contador de tentativas de uma chave usada em request_limit.
 *
 * @param string $key
 * @return void
 */
function request_limit_clear(string $key): void
{
    $session = new \Source\Core\Session();
    if ($session->has($key)) {
        $session->unset($key);
    }
}

/**
 * @param string $field
 * @param string $value
 * @return bool
 */
function request_repeat(string $field, string $value): bool
{
    $session = new \Source\Core\Session();
    if ($session->has($field) && $session->$field == $value) {
        return true;
    }

    $session->set($field, $value);
    return false;
}
