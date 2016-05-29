"use strict";
/** 
    * Ein kleines JS-Modul, dass in unserer Webseiten eingebunden werden kann, 
    * um Werbung/Hinweise im Rahmen unserer Serien "PhysikOnline-Unterstützt" (siehe Pott #1303 und http://physikonline.uni-frankfurt.de/portal/page/po-unterstuetzt).
    *
    * Geschrieben von Philip Arnold, 2016
    * 
    * Beschreibung:
    * Das Skript wurde extra so konzipiert, dass es Seiten unabhängig genutzt werden kann. Als einzige Voraussetzung braucht es jQuery und kann anschließend per 
    * <script src="/local/po-unterstuetzt/po-unterstuetzt.js" type="text/javascript"></script>
    * auf allen unseren Seiten vom zentralen Ablageort eingebunden werden. So behält man leicht den Überblick, wo welche Hinweise gerade aktiv geschaltet sind.
    * Zentraler Punkt ist die recht komplex aufgezogene "Config":
    *  - für jede Domain kann eine Liste von Hinweisen/Werbungen eingerichtet werden.
    *  - wichtig war mir dabei, dass die Darstellung der Hinweise für jede Domain/Webseite individuell gestalltet werden kann.
    *  - letztendlich ist sie nun sogar für jede eigene Werbung anpassbar.
    *  - es können je Domain null, eine oder auch durchaus mehrere Hinweise aktiv geschaltet sein. Im letzteren Fall wird zufällig ein Hinweis ausgewählt, der dann angezeigt wird.
    * Erklärung der Listenelemente:
    *  - name: derzeit nicht verwendet, aber schön um gleich zu erkennen, worum es in diesem Part geht,
    *  - active: damit lässt sich steuern, ob dieser Hinweis berücksichtigt werden soll oder nicht. Mögliche Werte true|false|object. 
    *            Mit einem Objekt lässt sich ein Zeitraum definieren bspw: {'start': '2016-05-29', 'stop': '2016-06-12'}
    *  - placeAd: Steht für 'place advertisement' und muss eine Function sein, die das platzieren und stylen des Hinweises auf der Webseite übernimmt.
*/
var pa_advertise = {
    configs: {
        'riedberg.tv': [
            {'name': 'DRK',
             'active': {'start': '2016-05-29', 'stop': '2016-06-12'},
             'placeAd': function(){
                 var image = '<a href="drk.de"><img src="https://elearning.physik.uni-frankfurt.de/local/po-unterstuetzt/DRK_Zeichen_Setzen_Helfer_Slogan.png" title="DRK.de" alt="" style="height:180px"/></a>',
                     text  = '<strong>RiedbergTV unterstützt</strong><br />'+
                             'Die nächsten Tage möchten wir auf die Arbeit des DRK aufmerksam machen: Es bewältigt große humanitäre Aufgaben, während die meisten es als selbstverständlich hinnehmen, aber das ist es nicht. '+
                             '<br /><strong>Hilf auch du!</strong> Werde Förderer, Blutspender oder <strong>ehrenamtlicher Helfer!</strong> Mehr Infos auf <a href="http://drk.de">drk.de</a> oder <a href="http://drkfrankfurt.de">drkfrankfurt.de</a>',
                   comment = '<br /><small>Diese Box wird im Rahmen von <a href="http://physikonline.uni-frankfurt.de/portal/page/po-unterstuetzt" title="Wir unterstützen!">&raquo;PhysikOnline-Unterstützt&laquo;</a> angezeigt.</small>';
              },
            },
            {'name': 'nightlineffm',
             'active': {'start': undefined, 'stop': undefined},
             'placeAd': function(){
                 var image = '',
                     text  = '',
                   comment = '<br />';
              },
            },
        ],
        'elearning.physik.uni-frankfurt.de': [
            {'name': 'DRK',
             'active': {'start': '2016-05-29', 'stop': '2016-06-12'},
             'placeAd': function(){
                 var image = '<a href="drk.de"><img src="https://elearning.physik.uni-frankfurt.de/local/po-unterstuetzt/DRK_Zeichen_Setzen_Helfer_Slogan.png" title="DRK.de" alt="" style="height:180px"/></a>',
                     text  = '<strong>&raquo;PhysikOnline unterstützt&laquo;</strong><br />'+
                             'Die nächsten Tage möchten wir auf die Arbeit des DRK aufmerksam machen: Es bewältigt große humanitäre Aufgaben, während die meisten es als selbstverständlich hinnehmen, aber das ist es nicht. '+
                             '<br /><strong>Hilf auch du!</strong> Werde Förderer, Blutspender oder <strong>ehrenamtlicher Helfer!</strong> Mehr Infos auf <a href="http://drk.de">drk.de</a> oder <a href="http://drkfrankfurt.de">drkfrankfurt.de</a>',
                   comment = '<br /><small>Diese Box wird im Rahmen von <a href="http://physikonline.uni-frankfurt.de/portal/page/po-unterstuetzt" title="Wir unterstützen!">&raquo;PhysikOnline-Unterstützt&laquo;</a> angezeigt.</small>';
                 var $parent = $('#po3-hauptseite-tile'),
                     $newbox = $('<div class="po3-tile-row"></div>');
                 // add image and text to $newbox
                 $newbox.css({'background-color': '#fff','border': '1px solid #b9b9b9', 'margin-top': '18px', 'padding':'8px'})
                    .append($(image).css({'float':'left', 'display':'block', 'width':'50%', 'text-align':'center', 'margin-right': '14px'}))
                    .append($('<div></div>').css({'text-align':'left'}).html(text+comment))
                    .append($('<div style="clear:both;" />'));
                 // insert $newbox into tile section
                 $($parent.children('.po3-tile-row')[3]).before($newbox);
              },
            },
            {'name': 'nightlineffm',
             'active': {'start': undefined, 'stop': undefined},
             'placeAd': function(){
                 var image = '',
                     text  = '';
              },
            },
        ],
    },
    
    init: function(){
        // check on which page the script runs
        var domain = window.location.hostname;
        //console.log(domain);
        
        // check if config is available for this domain
        var config = this.chooseConfig(domain);
        if (config){
            //console.log(config);
            config.placeAd();
        }
    },
    
    chooseConfig: function(key){
        var today = new Date(),
            filteredConfigs = [];
        // search for active configs
        $.each(this.configs[key], function(i, c){
            var start = (c.active && c.active.start)? new Date(c.active.start) : undefined,
                stop  = (c.active && c.active.stop) ? new Date(c.active.stop)  : undefined,
                isActive = false;
                
            if (start === undefined && stop === undefined)
                isActive = (c.active == true);
            else if (stop === undefined)
                isActive = (start <= today);
            else
                isActive = (start <= today && stop > today);
            
            // configuration is still active and has defined function 'placeAd'
            if (isActive && (typeof c.placeAd === 'function'))
                filteredConfigs.push(c);
        });
        
        // are there any configs left?
        if (filteredConfigs.length > 1){
            // randint between 0 (inclusive) and filteredConfigs.length (exclusive)
            var randomkey = Math.floor(Math.random() * (filteredConfigs.length - 0)) + 0;
            return filteredConfigs[randomkey];
        }
        else if (filteredConfigs.length == 1)
            return filteredConfigs[0];
        else
            return null;
    },
};

$( document ).ready(function() {
    setTimeout(function(){ pa_advertise.init(); }, 100);
    //pa_advertise.init();
});