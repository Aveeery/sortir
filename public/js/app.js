(function($) {
    $(document).ready(function() {
        showPlaces();
    });

    function showPlaces()
    {
        $('#event_city').change(function(event){
            var id = $("#event_city option:selected");

            // TODO modifier url + problème affichage ancien lieu sélectionné
            var url = 'http://localhost/sortir/public/places';

            $.ajax({
                url: url,
                type: "GET",
                data: { id: id.val()},

                success: function(data) {
                    var places = JSON.parse(data);

                    $("#event_place option").hide();
                    for(var i=0; i<places.length; i++){

                        var idPlace = places[i];
                        $("#event_place option[value=" + idPlace + "]").show();
                    }

                    /*
                    Méthode qui vide le select et le remplit uniquement avec les valeurs nécessaires

                    $('#event_place').empty();

                        for(var i=0; i<places.length; i++){
                        $('#event_place').append($('<option></option>').attr('value', places[i].slice(0)).text(places[i].slice(1)));
                    }*/
                },
                error: function(){
                    alert("Erreur");
                }
            })
        })
    }

})(jQuery);