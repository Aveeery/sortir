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
                    var places = JSON.parse(data)

                    //Méthode qui cache les lieux qui ne sont pas dans la ville sélectionnée

                    /*$("#event_place option").hide();
                    for(var i=0; i<places.length; i++){

                        var idPlace = places[i];
                        $("#event_place option[value=" + idPlace + "]").show();
                    }*/


                    //Méthode qui vide le select et le remplit uniquement avec les valeurs nécessaires

                    $('#event_place').empty();

                    for(var i=0; i<places.length; i++){
                        $('#event_place').append($('<option>' + places[i][1] + '</option>').attr('value', places[i][0]));
                    }
                },
                error: function(){
                    alert("Erreur");
                }
            })
        })
    }

})(jQuery);