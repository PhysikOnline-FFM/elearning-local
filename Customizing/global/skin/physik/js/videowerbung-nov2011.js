// Werbeaktion ab 08.11.2011

        // etwas no-js und js-only-magic
        $(function(){
                $(".no-js").hide();  $(".js-only").show();
        });

        ad_index_cnt = 0; // globaler counter der Durchlaeufe zaehlt
        $(function(){
                (ad=$('.PE-bewerbung-ad .slider')).cycle({
                                fx: 'scrollLeft',
                                speed: 700,
                                timeout: 100,
                                timeoutFn: function(cur, next, opts, isFor) {
                                        // mehr timeout bei der dritten folie.
                                        // schneller erster Durchlauf
                                        var index = opts.currSlide; ad_index_cnt++;
                                        switch(ad_index_cnt) {
                                                case 1: case 2: return 200;
                                                case 3: return 4000;
                                                default: return 3000;
                                        }
                                }
                });
        });
