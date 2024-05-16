$(document).ready(function() {
    //inicializace UI pro dialogove okno vytvorit schuzku
    $(".dialog-vytvorit_schuzku").dialog({
        autoOpen: false, // Dialog bude na začátku skrytý
        draggable: false,
        position: {
            my: "top center", //bod dialogoveho okna ktery chci zarovnat
            at: "top center", //bod ciloveho elementu na ktery chci zarovnat
            of: ".container"
        } 
    });
     //inicializace UI pro dialogove okno schuzky-obchodniku
    $(".dialog-schuzky-obchodniku").dialog({
        autoOpen: false, // Dialog bude na začátku skrytý
        draggable: false  
    });
    //inicializace UI pro dialogove okno vsechny schuzky
    $(".dialog-vsechny-schuzky").dialog({
        autoOpen: false, // Dialog bude na začátku skrytý
        draggable: false 
    });


    $(".vytvorit-schuzku_btn").on("click", function() {
        $(".dialog-vytvorit_schuzku").dialog("open").parent().draggable();
    });

    $(".schuzky-obchodniku_btn").on("click", function() {
        $(".dialog-schuzky-obchodniku").dialog("open").parent().draggable();
    });

    $(".vsechny-schuzky_btn").on("click", function() {
        $(".dialog-vsechny-schuzky").dialog("open").parent().draggable();
    });

    $(".close-vytvorit-schuzku").on("click", function(){
        $(".dialog-vytvorit_schuzku").dialog("close")
    });

    $(".close-schuzky-obchodniku").on("click", function(){
        $(".dialog-schuzky-obchodniku").dialog("close")
    });

    $(".close-vsechny-schuzky").on("click", function(){
        $(".dialog-vsechny-schuzky").dialog("close")
    });



    //validace formuláře vytvořit schůzku
    $("#submit-vytvorit_schuzku").on("click", (event)=>{
        if ($("#vytvorit_schuzku-jmeno").val().length > 30 ){
            $(".error-hlaska.error-jmeno").css({"display": "block"})
            $(".error-hlaska.error-jmeno").text("Jméno může mít maximálně 30 znaků")
            event.preventDefault(); 
        }
        if ($("#vytvorit_schuzku-prijmeni").val().length > 30 ){
            $(".error-hlaska.error-prijmeni").css({"display": "block"})
            $(".error-hlaska.error-prijmeni").text("Příjmení může mít maximálně 30 znaků")
            event.preventDefault(); 
        }
        if ($("#vytvorit_schuzku-poznamka").val().length > 180 ){
            $(".error-hlaska.error-poznamka").css({"display": "block"})
            $(".error-hlaska.error-poznamka").text("Poznámka může mít max 180 znaků")
            event.preventDefault();      
        }
        if(!validaceTel())
            event.preventDefault();      
        } 
    )
    function validaceTel() {
        // Získání hodnoty z inputu a odstranění všech mezer
        let vyslednyString = $("#vytvorit_schuzku-tel").val().replace(/\s+/g, '');
    
        // Funkce pro kontrolu, zda jsou všechny znaky v řetězci čísla
        function isAllNumeric(str) {
            return /^\d+$/.test(str);
        }
    
        // Kontrola, že první znak je + nebo -
        if (vyslednyString.length > 0 && (vyslednyString[0] === '+' || vyslednyString[0] === '-')) {
            // Povolí '+' nebo '-' pouze na začátku, odstraní ostatní tyto znaky v řetězci
            vyslednyString = vyslednyString[0] + vyslednyString.slice(1).replace(/[-+]/g, '');
            if (!isAllNumeric(vyslednyString.slice(1))) {
                $(".error-hlaska.error-tel").css({"display": "block"});
                $(".error-hlaska.error-tel").text("Telefonní číslo není ve správném formátu");
                return false;
            } else {
                $("#vytvorit_schuzku-tel").val(vyslednyString);  
            }
        } else if (!isAllNumeric(vyslednyString)) {
            $(".error-hlaska.error-tel").css({"display": "block"});
            $(".error-hlaska.error-tel").text("Telefonní číslo není ve správném formátu");
            return false;
        }
    
        // Kontrola délky stringu
        if (vyslednyString.length >= 9 && vyslednyString.length <= 13) {
            return true;
        } else {
            $(".error-hlaska.error-tel").css({"display": "block"});
            $(".error-hlaska.error-tel").text("Telefonní číslo není ve správném formátu");
            return false;
        }
    }
    // Zobrazení flashMessage při odeslání formuláře
        let flashMessage = $(".flashMessage");
        if (flashMessage.text().trim().length > 0) {
            flashMessage.show(); // Zobrazí flash zprávu

            // Nastaví timeout pro skrytí zprávy
            setTimeout(function() {
                flashMessage.fadeOut(); 
            }, 2000);
        }

        
    /* Kontrola, zda byla změna hodnota comboboxu v dialogu všechny schůzky a odeslání formuláře*/ 
    $(document).on("change", ".schuzky-obchodniku-id", function(event) {
        let selectedValue = $(this).val();
        let hiddenInputValue = $(this).siblings('.hidden').val();
        $("#noveIdObchodnika").val(selectedValue);
        $("#idSchuzky").val(hiddenInputValue);
        $("#noveIdSubmit").click()
    });
});

