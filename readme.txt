=== Poprawna Odmiana ===
Contributors: lysiu
Donate link: http://lysiu.pl/donate
Tags: komentarze, poprawnie, słownie
Requires at least: ???
Tested up to: 2.8.4
Stable tag: 0.7a

== Description ==

W wersji 0.7 dodałem odmianę daty w komentarzach, wcześniej o tym zapomniałem, ale jako, że nikt nie ocenia ani nie komentuje tej wtyczki, nie mogłem o tym wiedzieć.
Dodatkowo planuje przeniesienie wszystkiego do filtrów gdyż teraz użycie the_time() na przykład w stopce powoduje również odmianę, co jest raczej niepożądane.
Póki aby to obejść wystaczy wpisać w footer.php w stopce stałą, bądź dodać własną funkcję w ustawieniach pluginu.

Zmienia sposób wyświetlania linku komentarzy (a od wersji 0.6 również daty).

&sect; Konfiguracja:

<ul>
<li><strong>Format wyświetlania</strong>: [komentarzy] jest zamieniane na odmienione słowo ‘komentarz’, a [liczba] na liczbę komentarzy.</li>
<li><strong>Format wyświetlania daty</strong>: [dzien], [rok], [miesiac], a [] jest zamieniane na '-go'.</li>
<li><strong>Format liczby</strong>: do wyboru slownie lub liczba. Jeśli wybierzemy slownie to np.: 1478 zostanie zamienione na: tysiąc czterysta siedemdziesiąt osiem. Maxymalna liczba komentarzy, która zostanie zamieniona na słowa to 9999. Przy większej liczbie komentarzy ilość nie zostanie zamieniona na postać słowną, niezależnie od ustawień. Zwiększenie tego limitu wymaga dodania kilku linijek kodu, więc to żaden problem… </li>
<li><strong>Z dużej litery</strong>: do wyboru yes lub no – zamienia pierwszą literę tekstu na dużą.</li>
<li><strong>Wyświetlane przy braku</strong>: ten tekst zostanie wyświetlony gdy nie ma żadnych komentarzy.</li>
</ul>

[<a href="http://lysiu.pl/?cat=13" title="zobacz jak to wygląda z poniższymi ustawieniami">Przykładowa konfiguracja</a>]:
<ul>
<li><strong>Format wyświetlania</strong>: [komentarzy]: [liczba]!</li>
<li><strong>Format wyświetlania daty</strong>: [dzien] [miesiac], [rok] roku</li>
<li><strong>Format liczby</strong>: slownie</li>
<li><strong>Z dużej litery</strong>: yes</li>
<li><strong>Wyświetlane przy braku</strong>: Komentarzy: brak!</li>
</ul>

&sect; Używanie własnej funkcji (>=0.5)



&raquo; Zmienne &laquo; 

<ul>
<li><strong>$wynik</strong> - wartość tej zmiennej zostanie wyświetlona po wykonaniu kodu.</li>
<li><strong>$pat</strong> - calość przeformatowanego tekstu.</li>
<li><strong>$ilosc</strong> - liczba komentarzy</li>
<li><strong>$id</strong> - id wpisu</li>
<li><strong>$liczba</strong> - przeformatowana liczba komentarzy</li>
<li><strong>$komentarzy</strong> - odmienione słowo 'komentarz'</li>
<li><strong>$rok</strong> - rok, w którym został opublikowany post</li>
<li><strong>$miesiac</strong> - miesiąc, w którym został opublikowany post</li>
<li><strong>$dzien</strong> - dzień, w którym został opublikowany post</li>
<li><strong>$komentarzy</strong> - odmienione słowo 'komentarz'</li>
<li><strong>$go</strong> - czyli poprostu '-go' ;-)</li>
</ul>

&raquo; Przykład &laquo; 

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

<p>&nbsp;</p>
&raquo; Uwagi &laquo; 

<ul>
<li>Błędny kod zostanie zignorowany i zostanie wyświetlona nie zmieniony $pat</li>
<li>Nie można używać \ (back-slash)</li>
<li>Najlepiej używać prefixów dla zmiennych</li>
<li>W razie problemów wystarczy wyłączyć "Użyj własnego kodu" i zapisać ustawienia</li>
</ul>



<p style="font-style: italic;">Mam nadzieję, że wszystko jest ok, ale jeśli znajdziecie jakieś błędy poinformujcie mnie.
Uwagi i pytania proszę umieszczać w komentarzach do tego [<a href="http://lysiu.pl/?cat=13" title="...">postu</a>]. W razie problemów z wersją 0.5/0.6 zawsze możesz wrócić do starszej.</p>

== Installation ==

Rozpakuj do wp-content/plugins i aktywuj plugin.



== Frequently Asked Questions ==

= Błędy =
Nie można używać \ (back-slash) w kodzie własnej funkcji
Nie wiem czy na wszystkich skórkach będzie działać opcja odmiany daty ;/

== Screenshots ==

1. Formularz do konfiguracji
2. Własny kod
3. Konfiguracja: [liczba] [komentarzy]!, slownie, yes
4. Konfiguracja: [liczba] [komentarzy]!, liczba, yes

== Changelog ==
= 0.7a   2009-12-09-04:08 =
* Działa w php4

= 0.7    2009-10-11-20:08 =
* Teraz Odmienia również date w komentarzach

= 0.6    2009-10-08-00:22 =
* kod przepisany z języka strukturalnego na obiektowy
* dodałem poprawne formatowanie daty
* inne poprawki (:

= 0.5    2009-09-29-21:10 =
* poprawiłem co się dało xo
* dodałem możliwość zdefiniowania własnej funkcji

= 0.4-r6 2009-09-29-15:16 =
* zaktualizujcie - poprawione przerwy między słowami!!!

= 0.4-r5 2009-09-29-13:30 =
* oficjalne repozytorium

= 0.4    2009-09-29-02:29 =
* zniosłem ograniczenie 9999 dla wyświetlania w postaci liczbowej
* poprawiłem literówki


= 0.3 =
* dodałem wp_nonce_field i check_admin_referer


= 0.2 =
* poprawiłem literówki


= 0.1 =
* pierwsze wydanie


`<?php code(); // goes in backticks ?>`
