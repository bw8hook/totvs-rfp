<x-app-layout>
    <div class="" style=" padding-bottom: 130px;">
        <div class="max-w-full mx-auto">

            <div id="titleComponent_KnowledgeBase" style=" padding-top: 20px; min-height: 100px; height: auto; justify-content: space-between; align-items: flex-start;" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" >  
                <div class="block" style="width: 35%; margin: 50px auto;">    
                    <div style="width: 100%; text-align:center;">
                        <h3 style="color:#3A57E8; font-size: 24px; font-weight: 600; line-height: 28.6px; margin-bottom:20px;">Enviado para processamento</h3>
                        <p style="font-size: 20px; font-weight: 100; line-height: 28.6px; color:#525B75;">Estamos enviando seu arquivo para nossa base de dados e você pode acompanhar o status de envio na página Base de Conhecimento.</p>
                    </div>
                    <div class="btns_bottom" style="border-top:none; margin-top: 0px;">
                        <div class="AlignBtns">
                            <a href="{{ route('knowledge.list') }}" class="btn_finishSend">
                                <div class="alignCenter">
                                    <span>Base de Conhecimento</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


<script>
    // $(document).ready(function () {
    //     $(".side_menu_big").addClass("menu_hidden").removeClass("menu_visible");
    //     $(".side_menu_small").addClass("menu_visible").removeClass("menu_hidden");
    // });
</script>
