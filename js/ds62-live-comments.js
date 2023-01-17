jQuery(document).ready(function ($) {
    
    /*function getLiveComments (id) {
                
        let data = {
    		action: 'get_live_comments',
    		project_id: id
    		//pagenum: 1
    	};
        
        $.get( myajax.url, data, function(response) {
            
            $('#ds62-comments').find('.ds62-progress-animation').hide();
            
            $('#ds62-live-comments').append(response);
            
            $('.ds62-comments').jScrollPane({
               contentWidth: '0px',
               autoReinitialise: true
            });
            
            $('#ds62-projects .ds62-post-title-btn').click(function () {
                    
                ds62ProjectId = $(this).parents('div[data-elementor-type="loop"]').attr('data-post-id');
                console.log(ds62ProjectId);
                $('#ds62-live-comments').remove();
                $('#ds62-comments').find('.ds62-progress-animation').show();
                getLiveComments(ds62ProjectId);
                
            });
        
        }); 
    }*/
    
    /*// Выбираем целевой элемент
    let target = document.querySelector('#ds62-projects-post');
    
    // Конфигурация observer (за какими изменениями наблюдать)
    const config = {
        childList: true
    };
    
    // Колбэк-функция при срабатывании мутации
    const callback = function(mutationsList, observer) {
        let ds62ProjectId = $('#ds62-projects div.type-ds62-project').first().attr('data-post-id');
        getLiveComments (ds62ProjectId);
    };
    
    // Создаём экземпляр наблюдателя с указанной функцией колбэка
    const observer = new MutationObserver(callback);
    
    // Начинаем наблюдение за настроенными изменениями целевого элемента
    observer.observe(target, config);
    
    // Позже можно остановить наблюдение
    //observer.disconnect();
    */
});