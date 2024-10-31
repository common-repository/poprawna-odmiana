<?php
/*
 * Plugin Name: Poprawna odmiana
 * Plugin URI: http://lysiu.pl/?p=87
 * Description: Odmienia słowo 'komentarz' (a od wersji 0.6 również miesiąc), możliwość wyświetlania liczb słownie.
 * Version: 0.7a
 * Author: Piotr "łysiu" Łysek
 * Author URI: http://lysiu.pl/
 * Min WP Version: 2.0.4
 * Max WP Version: 2.8.4
 */
/*
Copyright 2009 Piotr Łysek  (kontakt@lysiu.pl)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
*/


// Klasa polska odmiana
class PolOdm
{
    // Liczby słownie
    var $liczby = array
    (     
        1 => 'jeden','dwa','trzy','cztery','pięć','sześć','siedem','osiem','dziewięć',
        'dziesięć','jedenaście','dwanaście','trzynaście','czternaście','piętnaście',
        'szesnaście','siedemnaście','osiemnaście','dziewiętnaście','dwadzieścia',
        30 => 'trzydzieści',
        40 => 'czterdzieści',
        50 => 'pięćdziesiąt',
        60 => 'sześćdziesiąt',
        70 => 'siedemdziesiąt',
        80 => 'osiemdziesiąt',
        90 => 'dziewięćdziesiąt',
        100 => 'sto',
        200 => 'dwieście',
        300 => 'trzysta',
        400 => 'czterysta',
        500 => 'pięćset',
        600 => 'sześćset',
        700 => 'siedemset',
        800 => 'osiemset',
        900 => 'dziewięćset'   
    );
    
    // Dni - narazie nie używane :x
    var $dni = array
    (
        1 => 'pierwszego', 'drugiego', 'trzeciego', 'czwartego', 'piątego', 'szóstego',
             'siódmego', 'ósmego', 'dziewiątego', 'dziesiątego', 'jedenastego', 'dwunastego',
             'trzynastego', 'czternastego', 'pietnastego', 'szesnastego', 'siedemnastego',
             'osiemnastego', 'dziewietnastego', 'dwudziestego',
        30 => 'trzydziestego'
        
    );
    
    // Miesiące
    var $miesiace = array
    (
        1 => 'stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca',
             'sierpnia', 'września', 'października', 'listopada', 'grudnia'
    );
    
    // Domyślne ustawienia
    var $domyslne = array
    (
        'polodm_wersja' => 0.7,
        'polodm_pat' => '[liczba] [komentarzy]',
        'polodm_format' => 'slownie',
        'polodm_duza_litera' => 'yes',
        'polodm_brak_komentarzy' => 'skomentuj',
        'polodm_uzyj_custom' => 'no',
        'polodm_custom' => '',
        'polodm_pat_daty' => '[dzien] [miesiac], [rok] roku',
        'polodm_uzyj_daty' => 'no',
        'polodm_uzyj_komentarzy' => 'yes'
    );
    
    // Konstruktor, dodaje opcje i rejestruje plugin w WP
    function __construct()
    {        
        // Dodaje opcje, ustawia ich domyślne wartości
        $wersja = get_option( 'polodm_wersja' );
        if( true /*empty( $wersja ) || ( $wersja < $this->domyslne['polodm_wersja'] )*/ )
        {
            foreach( $this->domyslne as $opcja => $wartosc )
            { $stara = get_option( $opcja );
              if( empty( $stara ) )
                update_option( $opcja, $wartosc, '', 'yes' );
            }
        }

        // Funkcja, która zostanie wywołana by wyświetlić ilość komentarzy
        add_action ( 'comments_number', array( &$this, 'pokaz' ), 1, 5 );
        
        // Funkcja, która zostanie wywołana w celu wyświetlenia daty
        // add_action ( 'the_time', array( &$this, 'data' ), 1, 5 );
        
        // Funkcja, która zostanie wywołana by wyświetlić w menu link z konfiguracją wtyczki
        add_action( 'admin_menu', array( &$this, 'dodaj_opcje' ) );
        
        // Zmienia datę w komentarzach
        add_action ( 'get_comment_date', array( &$this, 'data' ), 1, 5 );
    }
    
    // PHP4
    function PolOdm() { $this->__construct(); }
    
    // Dodaje link do konfiguracji wtyczki
    function dodaj_opcje()
    {
        // Wyświetla link
        add_options_page( 'Polska odmiana', 'Polska odmiana',
                          'manage_options', 'polskaodmiana', array( &$this, 'wyswietl_opcje' ) );
    }
    
    // Obsługa formularza konfiguracji
    function wyswietl_opcje()
    {
        // Gdy ktoś wciśnie przycisk "Zapisz"
        if( $_POST[ 'polodm_zmien' ] == 'T' )
        {
            // Sprawdza czy funkcja wywołana z właściwej strony
            check_admin_referer('polodm-update-options');
            
            // Nowe wartości
            echo '<div class="updated">';         
            $npat = $_POST['polodm_pat'];
            $npat_daty = $_POST['polodm_pat_daty'];
            $nformat = $_POST['polodm_format'];
            $nduza = $_POST['polodm_duza_litera'];
            $nbrak = $_POST['polodm_brak_komentarzy'];
            $ncustom = stripslashes($_POST['polodm_custom']);
            $nuzyj_custom = $_POST['polodm_uzyj_custom'];
            $nuzyj_daty = $_POST['polodm_uzyj_daty'];
            
            // Zapisuje nowe wartości
            if(!empty($nuzyj_custom))
            {
                update_option('polodm_uzyj_custom', $nuzyj_custom);
                update_option('polodm_custom', $ncustom);
            }
            else
                update_option('polodm_uzyj_custom', 'no');
            
            
            if ( substr_count( $npat, "[liczba]" ) < 1 || substr_count( $npat, "[komentarzy]" ) < 1 )
                echo '<p>Błąd w opcji "Format wyświetlania", zmienna nie została zmieniona!</p>';
            else ( update_option( 'polodm_pat', $npat ) );
            
            if ( substr_count( $npat_daty, "[rok]" ) < 1 || substr_count( $npat_daty, "[dzien]" ) < 1 )
                echo '<p>Błąd w opcji "Format wyświetlania daty", zmienna nie została zmieniona!</p>';
            else ( update_option( 'polodm_pat_daty', $npat_daty ) );
            
            if( $nformat != 'slownie' && $nformat != 'liczba' )
                echo '<p>Błąd w opcji "Format liczby", zmienna nie została zmieniona!</p>';
            else ( update_option( 'polodm_format', $nformat ) );
            
            if( !empty( $nduza ) )
                update_option( 'polodm_duza_litera', 'yes' );
                else update_option( 'polodm_duza_litera', 'no' );
            
            if( !empty( $nuzyj_daty ) )
                update_option( 'polodm_uzyj_daty', 'yes' );
                else update_option( 'polodm_uzyj_daty', 'no' );
            
            update_option( 'polodm_brak_komentarzy', $nbrak );
            
            echo ' <p><strong>Ustawienia zapisane!</strong></p></div>';
        }
        
        // Pobiera wartości opcji
        $pat = get_option( 'polodm_pat' );
        $pat_daty = get_option( 'polodm_pat_daty' );
        $format = get_option( 'polodm_format' );
        $duza = get_option( 'polodm_duza_litera' );
        $brak = get_option( 'polodm_brak_komentarzy' );
        $uzyj_custom = get_option( 'polodm_uzyj_custom' );
        $custom = get_option( 'polodm_custom' );
        $wersja = get_option( 'polodm_wersja');
        $uzyj_daty = get_option( 'polodm_uzyj_daty' );
        // Wyświetla formularz
?>
        <h2>Poprawna odmiana - Opcje <?php echo $wersja;?></h2>
        <form id="polodm" name="polodm_opcje" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <p class="submit">
            <input type="submit" name="Submit" value="Zapisz" />
        </p>
        <?php wp_nonce_field('polodm-update-options') ?>
        <input type="hidden" name="polodm_zmien" value="T" />
        
        <fieldset id="opcje">
            <p>
            <label for="polodm_pat">Format wyświetlania:</label>
                <input type="text" name="polodm_pat" id="polodm_pat" value="<?php echo $pat; ?>" size="35" />
            </p>
            <p>
            <label for="polodm_pat_daty">Format wyświetlania daty:</label>
                <input type="text" name="polodm_pat_daty" id="polodm_pat_daty" value="<?php echo $pat_daty; ?>" size="35" />
            </p>
            <p>
            <label for="polodm_format">Format liczby: ('slownie' lub 'liczba')</label>
                <input type="text" name="polodm_format" id="polodm_format" value="<?php echo $format; ?>" size="7" />           
            </p>
            <p>
            <label for="polodm_brak_komentarzy">Wyświetlane przy braku:</label>
                <input type="text" name="polodm_brak_komentarzy" id="polodm_brak_komentarzy" value="<?php echo $brak; ?>" size="35" />               
            </p>           
            <p>
            <label for="polodm_duza_litera">Z dużej litery</label>
            <input type="checkbox" name="polodm_duza_litera" id="polodm_duza_litera" value="yes" size="3" <?php echo ($duza=="yes"?"checked=\"checked\"":"");?>" /> 
            </p>
            <p>
            <label for="polodmu_uzyj_custom">Użyj własnego kodu php</label>                
            <input type="checkbox" name="polodm_uzyj_custom" id="polodm_uzyj_custom" value="yes" size="3" <?php echo ($uzyj_custom=="yes"?"checked=\"checked\"":"");?>" /> 
            </p>
            <p>
            <label for="polodm_uzyj_daty">Użyj odmiany daty</label>               
            <input type="checkbox" name="polodm_uzyj_daty" id="polodm_uzyj_daty" value="yes" size="3" <?php echo ($uzyj_daty=="yes"?"checked=\"checked\"":"");?>" /> 
            </p>
            <label for="polodm_custom">Wklej swój kod php:</label>
	</div>
            <p>
                <textarea id="polodm_custom" rows="10" cols="100" name="polodm_custom"><?php echo $custom; ?>
                </textarea>
            </p>         
        </fieldset> 
        </form>
            <p>
            <strong style="text-decoration: underline;">Zmienne</strong>:
            </p>
            <p></p>
            <p><strong>$W_POKAZ</strong> - prawda dla komentarzy.</p>
            <p><strong>$W_DATA</strong> - prawda dla daty.</p>
            <p><strong>$wynik</strong> - wartość tej zmiennej zostanie wyświetlona po wykonaniu kodu.</p>        
            <p><strong>$pat</strong> - calość przeformatowanego tekstu.</p>
            <p><strong>$ilosc</strong> - liczba komentarzy</p>
            <p><strong>$id</strong> - id wpisu</p>
            <p><strong>$liczba</strong> - przeformatowana liczba komentarzy</p>
            <p><strong>$komentarzy</strong> - odmienione słowo 'komentarz'</p>
            <p><strong>$rok</strong> - rok, w którym został opublikowany post</p>
            <p><strong>$miesiac</strong> - miesiąc, w którym został opublikowany post</p>
            <p><strong>$dzien</strong> - dzień, w którym został opublikowany post</p>
            <p><strong>$go</strong> - '-go' ;-)</p>
            <p></p>
            <p><strong style="text-decoration: underline;">Przykład</strong>:</p>
            <pre>
            
            if( $W_POKAZ ) // twój kod został wywołany w celu obróbki linku komentarzy
            {
                $liczba_rozlozona = $this->rozloz_liczbe( 8711 );
                $liczba_slownie = $this->zamien_na_slowa( $liczba_rozlozona );
                $odmienione_slowo_komentarz = $this->odmieniaj( $liczba_rozlozona );

                // Dla wpisu numer 48 zawsze będzie wyświetlana stała liczba komentarzy ( 8711 )
                // Reszta wyświetli się zgodnie z konfiguracją
                if ( $id == 48 )
                   $wynik = ucfirst($odmienione_slowo_komentarz) . ': '.$liczba_slownie."!";
                else $wynik = $pat;
            }
            
            elseif ( $W_DATA ) // twój kod został wywołany w celu obróbki daty
            {
                if( $id == 1 )
                {
                    $wynik = "30-go marca, 1963 roku";
                }
            }
            </pre>
            <p></p>
            <p><strong style="color:red;">Błędny kod zostanie zignorowany i zostanie wyświetlona niezmieniony $pat</strong></p>
            <p><strong style="color:red; text-decoration:underline;">!! W razie problemów poprostu wyłącz "Użyj własnego kodu" i zapisz ustawienia !!</strong></p>
<?php        
    } // function PolOdm::wyswietl_opcje
    
    // Tworzy array() z rozłożoną liczbą
    function rozloz_liczbe( $liczba = 0 )
    {
        // Kasujemy cyfry, których nie będziemy sprawdzać 
        if( $liczba > 9999 ) $liczba = ($liczba%10000);
        
        // Liczba ma wartość zero
        if( !$liczba ) return NULL;

        
        $g = array();
        
        $r = str_split( $liczba );
        
        
        // Tysiące, ...
        $g['t'] = $liczba > 999 ? $r{0} : 0;
        
        // Setki, ...
        $g['s'] = $liczba > 99  ? ( ( $liczba < 1000 ) ? $r{0} : $r{1} ) : 0;
        
        // Dziesiątki, ...
        $g['d'] = $liczba > 9 ? ( $liczba < 100 ? $r{0} : ( $liczba < 1000 ? $r{1} : $r{2} )  ) : 0;
        
        // Jedności, ...
        $g['j'] = $liczba < 10 ? $r{0} : ( $liczba < 100 ? $r{1} : ( $liczba < 1000 ? $r{2} : $r{3} )  );

        return $g;
        
    }
    
    // Zamienia rozłożoną liczbę na słowa
    function zamien_na_slowa( $rozlozona )
    {
        // To się nie powinno zdażyć, ale...
        if( $rozlozona === NULL ) return "(NULL)";
        
        $liczby = $this->liczby;
        
        // Czy to już kolejna cyfra?
        $poprzednia = FALSE;
        
        $slownie = '';
        
        $tysiace = 'tysiąc';
        
        // Tysiące
        if( $rozlozona['t'] )
        {
            
            $poprzednia = TRUE;
            
            if( $rozlozona['t'] > 1 && $rozlozona['t'] < 5 )
                $tysiace = ' tysiące';
            elseif ( $rozlozona['t'] > 4 )
                $tysiace = ' tysięcy';

            $slownie .= ( $rozlozona['t'] > 1 ) ? $liczby{$rozlozona['t']} : '';
            $slownie .= $tysiace;
            
        }
        
        // Setki
        if( $rozlozona['s'] ) { $slownie .= ( $poprzednia ? ' ' : '' ) . $liczby{$rozlozona['s']*100}; $poprzednia = TRUE; }
        
        // Dziesiątki
        if( $rozlozona['d'] ) 
        {
            $slownie .= ( $poprzednia ? ' ' : '' );
            if( $rozlozona['d'] > 1 )
                $slownie .= $liczby{$rozlozona['d']*10};
            else
                $slownie .= $liczby{$rozlozona['d']*10+$rozlozona['j']};

            $poprzednia = TRUE;
            
        }
        
        // Jedności
        if ( $rozlozona['j'] && ( $rozlozona['d'] > 1 || !$rozlozona['d'] ) )
            {
                $slownie .= ( $poprzednia ? ' ' : '' );
                $slownie .= $liczby{$rozlozona['j']};
            }
            
        return $slownie;
        
    }
    
    // Odmienia słowo komentarz
    function odmieniaj( $rozlozona )
    {
        
        $komentarzy = 'komentarz';
        if( $rozlozona['t']||$rozlozona['s']||$rozlozona['d'] ) $komentarzy = 'komentarzy';
        if( $rozlozona['j'] == 1 ) return  $komentarzy;
        if( $rozlozona['j'] > 1 && $rozlozona['j'] < 5 && $rozlozona['d'] != 1 )
            $komentarzy = 'komentarze';
        else $komentarzy = 'komentarzy';
        
        return $komentarzy;
        
    }
    
    // Wyświetla wszystko
    function pokaz( $pierwszy = false, $drugi = false, $trzeci = false, $lnumber = 0 )
    {
        
        $id = $GLOBALS['id'];
        $liczba = $ilosc = get_comments_number( $id );
        
        // Pobiera opcje
        $duza = trim(get_option( 'polodm_duza_litera' ));
        $pat = get_option( 'polodm_brak_komentarzy' );
        $custom = stripslashes(get_option( 'polodm_custom' ));
        $uzyj_custom = trim(get_option( 'polodm_uzyj_custom'));
        $wynik = '';
    
        if( $ilosc )
        {
            $rozlozone = $this->rozloz_liczbe( $liczba );
        
            $pat = get_option( 'polodm_pat' );
            $format = get_option( 'polodm_format' );
            
            // 
            if( $ilosc < 10000 && $format == 'slownie' )  
                $liczba = $this->zamien_na_slowa( $rozlozone );
            
            // Sprawdza czy nie było samych zer po użyciu %
            if( $ilosc > 9999 && !($ilosc%10000))
                $komentarzy = "komentarzy";
            else $komentarzy = $this->odmieniaj( $rozlozone );
            
            $pat = str_replace( '[liczba]', $liczba, $pat );
            $pat = str_replace( '[komentarzy]', $komentarzy, $pat );
        }
        
        
        if( $duza == 'yes' )
            $pat = ucfirst( $pat );
        
        $sprawdz = str_replace(' ', '', $custom );
        $sprawdz = str_replace("\t", '', $sprawdz);
        
        // Beznadziejnie... 
        if( $uzyj_custom == 'yes' && !empty($sprawdz) )
        {
            $W_POKAZ = TRUE;
            $W_DATA = FALSE;
            
            if( @eval( $custom ) !== FALSE )
            {
                if(!empty($wynik)) $pat = $wynik;    
            }
            
        }
        echo $pat;
    
    }
    
    // Niewiele zmienia
    function data( $d = '' )
    {      
        $id = $GLOBALS['id'];
        
		if( get_option( 'polodm_uzyj_daty' ) != 'no' && strpos("Y", $d ) !== false && strpos("n", $d) !== false )
		{
			$pat_daty = get_option( 'polodm_pat_daty' );
			$uzyj_custom = trim(get_option( 'polodm_uzyj_custom'));
			$custom = stripslashes(get_option( 'polodm_custom' ));        
        
        
			$rok = get_post_time('Y', false, $id, true);
			$miesiac = get_post_time('n', false, $id, true);
			$dzien = get_post_time('j', false, $id, true);
			$go = '-go';
        
			$sprawdz = str_replace(' ', '', $custom );
			$sprawdz = str_replace("\t", '', $sprawdz);
        
        
			$pat_daty = str_replace( '[dzien]', $dzien, $pat_daty );
			$pat_daty = str_replace( '[rok]', $rok, $pat_daty );
			$pat_daty = str_replace( '[miesiac]', $this->miesiace[$miesiac], $pat_daty );
			$pat_daty = str_replace( '[]', $go, $pat_daty );
        
			if( $uzyj_custom == 'yes' && !empty($sprawdz) )
			{
				$W_POKAZ = FALSE;
				$W_DATA = TRUE;
            
				if( @eval( $custom ) !== FALSE )
				{
					if(!empty($wynik)) $pat_daty = $wynik;    
				}	
			}
        
			echo $pat_daty;
		}
	
		else 
		{
			echo $d;		
			return;
		}
	}
		
} // class PolOdm

// -----------------------------------------------------------
$PolskaOdmiana = new PolOdm();
?>