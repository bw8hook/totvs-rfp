import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


$( document ).ready(function() {

    $("#rowAdder").on('click', function(){

        console.log($("#CardForm textarea:last-child").val())

       var countDiretriz = ($('.diretriz').length)+1;
        const newRowAdd =
            '<div class="diretriz">' +
            '<label class="block font-medium text-sm text-gray-700" for="name"> Diretriz '+countDiretriz+':</label> ' +
            '<textarea name="NovaDiretriz['+countDiretriz+']" id="inputDescriptionEs" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" name="description_es" required></textarea>' +
            '</div>';

        $('#ListInputs').append(newRowAdd);
    });

    $("body").on("click", "#DeleteRow", function () {
        $(this).parents("#row").remove();
    })

});


