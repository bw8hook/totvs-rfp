import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { Portuguese } from "flatpickr/dist/l10n/pt.js";

import lottie from "lottie-web";

// Inicializa a animação quando o DOM estiver carregado
document.addEventListener("DOMContentLoaded", function () {
    lottie.loadAnimation({
        container: document.getElementById("lottie-container"),
        renderer: "svg",
        loop: true,
        autoplay: true,
        path: "/icons/lottie/loading.json", // Caminho gerado pelo Storage
    });


    lottie.loadAnimation({
        container: document.getElementById("lottie-container2"),
        renderer: "svg",
        loop: true,
        autoplay: true,
        path: "/icons/lottie/loading.json", // Caminho gerado pelo Storage
    });

    window.showAlertBootstrap = function (type, message, autoclose = true ) {
        let alertContainer = $("#alert-global");
    
        let alertClass = "";
        switch (type) {
            case "success": alertClass = "alert-success"; break;
            case "error": alertClass = "alert-danger"; break;
            case "info": alertClass = "alert-info"; break;
            case "warning": alertClass = "alert-warning"; break;
            default: alertClass = "alert-primary";
        }
    
        alertContainer.html(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <pre>${message}</pre>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `).fadeIn();
    
        $("#contentBody").animate({ scrollTop: 0 }, 500);
            
        if(autoclose){
            setTimeout(() => {
                alertContainer.fadeOut();
            }, 5000);
       }
       
    };
    

});

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


