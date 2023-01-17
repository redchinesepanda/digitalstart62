jQuery(document).ready(function($) {

    let ds62StageId, ds62ProjectId, activeProject, activeStage, activeStageId, activeProjectId, nameBlock, expiredBlock, notificationBlock, expiredDateBlock;
    let ds62ColumnProjects = $('#ds62-projects-post'), ds62ColumnStages = $('#ds62-stages-post'), ds62ColumnTasksAvaible = $('#ds62-avaible-post'), ds62ColumnTasksProgress = $('#ds62-progress-post'), ds62ColumnTasksCheck = $('#ds62-check-post'), ds62ColumnTasksDone = $('#ds62-done-post');
    
    /*let showIframeLoading = function() {
        let curLength = 0;
        let interval = setInterval(function() {
            if ($('iframe').length !== curLength) {
                curLength = $('.column-header').length;
                $('.mfp-iframe-scaler').append('<div class="ds62-progress-animation" style="background: #fff; width: 100%; height: 100%;"><img style="height: 30%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" src="http://digitalstart62.ru/wp-content/themes/astra-child/img/Spin-1s-200px.svg"/></div>');
                //$('.mfp-preloader').show();
    
            }
        }, 50);
        this.content.find('iframe').on('load', function() {
            clearInterval(interval);
            $('.mfp-content').find('.ds62-progress-animation').remove();
            //$('.mfp-preloader').hide();
        });
    };*/
	
	/*acf render*/
	
	function renderPage() {
		acf.do_action('ready', $('body'));

		// will be used to check if a form submit is for validation or for saving
		/*let isValidating = false;

		acf.add_action('validation_begin', () => {
			isValidating = true;
		});

		acf.add_action('submit', ($form) => {
			isValidating = false;
		});*/
		
		/*$('.acf-form').on('submit', function(e){
			e.preventDefault();
		});

		acf.add_action('submit', function($form){
			$.ajax({
				url: window.location.href,
				method: 'post',
				data: $form.serialize(),
				success: () => {
					ds62ColumnTasksAvaible.animate({'opacity':'0','left':'-50%'}, 600, function () {
						ds62ColumnTasksProgress.animate({'opacity':'0','left':'-50%'}, 600);
						ds62ColumnTasksCheck.animate({'opacity':'0','left':'-50%'}, 600);
						ds62ColumnTasksDone.animate({'opacity':'0','left':'-50%'}, 600);
						detachColumnsTasksChildren([ds62ColumnTasksAvaible, ds62ColumnTasksProgress, ds62ColumnTasksCheck, ds62ColumnTasksDone]);
						showLoadingAnimation();

						let activeStageIdAjax = $('#ds62-stages-post-wrapper .ds62-active-post-stage').parents('div[data-elementor-type="loop"]').attr('data-post-id');

						getPostsTasksAvaible(activeStageIdAjax);
						getPostsTasksProgress(activeStageIdAjax);
						getPostsTasksCheck(activeStageIdAjax);
						getPostsTasksDone(activeStageIdAjax);
					});
					acf.unlockForm($form);
				}
			});
		});*/

		$('.acf-form').on('submit', (e) => {
			
			let $form = $(e.target);
			$form.find('.acf-button').css({'pointer-events':'none'});
			e.preventDefault();
			// if we are not validating, save the form data with our custom code.
			//if( !isValidating ) {
				// lock the form
			acf.lockForm( $form );
				//
			$.ajax({
				url: window.location.href,
				method: 'post',
				data: $form.serialize(),
				success: () => {
				  // unlock the form
					acf.unlockForm( $form );
					refreshTaskBlocksAfterUpdateIframe();
				}
			});
			//}
		});
	}
	
	/*Функция переключения меню в мобильной версии*/
	
	function switcherMobile() {
		//if (!$('.ds62-left-gumburger__switcher').hasClass('ds62-left-gumburger__switcher-blocked')) {
			$('.ds62-left-gumburger').click(function () {
			//$(document).on('vclick', '.ds62-left-gumburger', function () {
					$('.ds62-left-gumburger__switcher').toggleClass('ds62-left-gumburger__switcher-active');
					//$('.ds62-left-gumburger__switcher').css({'transform':'rotate(360deg)'});
					$('.ds62-left-gumburger__list').toggleClass('ds62-left-gumburger__list-active');
			});
		//}
	}
	
	/*Функция добавления tooltip'а для иконок с классом ds62-notification и его обработчик*/
	
	function notificationToolTip() {
		$('.ds62-notification .elementor-widget-container').append('<div class="ds62-tooltip-notification">В задаче произошли изменения</div>');
		
		if ($(window).width() > 1000) {
			$('.ds62-notification .elementor-widget-container').mouseover(function () {
				notificationBlock = $(this).find('.ds62-tooltip-notification');
				notificationBlock.addClass('ds62-tooltip-notification-active');
			}).mouseout( function () {
				notificationBlock.removeClass('ds62-tooltip-notification-active');
			});
		}
		
	}
	
	/*Функция обработки клика на иконку "i", убирающая класс ds62-notification*/
	
	function removeNotification() {
		$('.elementor-element-5e565ba.ds62-notification').click(function () {
			let tooltipForRemove = $(this).find('.ds62-tooltip-notification');
			tooltipForRemove.remove();
			$(this).removeClass('ds62-notification');
		});
	}
	
	/*Функция обработки прогресс-бара*/
	
	function progressBar (selector, hash) {
		let ds62All = selector.find('div.type-ds62-'+hash+' .ds62-section-post');
		$.each(ds62All, function (index, element) {
			let parsedData = $.parseJSON($(element).find('#ds62-'+hash+'-tasks-status .elementor-shortcode').text());
			let percent = 0;
			if (parsedData.avaible == 0 && parsedData.progress == 0 && parsedData.check == 0 && parsedData.done == 0) {
				percent = 0;
			}else{
				percent = (((parsedData.done / (parsedData.avaible + parsedData.progress + parsedData.check + parsedData.done)).toFixed(2)) * 100).toFixed();
			}
			
			/*if (percent == 0) {
				$(element).find('.ds62-progress-bar .ds62-progress-bar__title').text('Нет завершенных задач');
			}else{*/
				$(element).find('.ds62-progress-bar .ds62-progress-bar__title').text('Завершено на ' + percent + '%:');
			/*}*/
			$(element).find('.ds62-progress-bar').animate({'opacity':'1'}, 400, function () {
				$(element).find('.ds62-progress-bar .ds62-progress-bar__line').animate({'width':percent+'%'}, 400);
			});
		}); 
	}
	
	/*Функция перезагрузки прогресс-бара*/
	
	function progressBarReloadStage(currentStageId, currentProjectId) {
		
		let data = {
    		action: 'get_tasks_sbs',
    		stage_id: currentStageId,
			project_id: currentProjectId
    	};
	    
        $.post( myajax.url, data, function(response) {

			let parsedData = $.parseJSON(response);
			
			$('.ds62-active-post-stage').find('.ds62-progress-bar').animate({'opacity':'0'}, 400, function () {
				let percent = 0;
				if (parsedData.stage_status.avaible == 0 && parsedData.stage_status.progress == 0 && parsedData.stage_status.check == 0 && parsedData.stage_status.done == 0) {
					percent = 0;
				}else{
					percent = (((parsedData.stage_status.done / (parsedData.stage_status.avaible + parsedData.stage_status.progress + parsedData.stage_status.check + parsedData.stage_status.done)).toFixed(2)) * 100).toFixed();
				}
				
				if (percent == 0) {
					$(this).find('.ds62-progress-bar__title').text('Нет завершенных задач');
				}else{
					$(this).find('.ds62-progress-bar__title').text('Завершено на ' + percent + '%:');
				}
				$(this).animate({'opacity':'1'}, 400, function () {
					$(this).find('.ds62-progress-bar__line').animate({'width':percent+'%'}, 400);
				});
			});
			
			$('.ds62-active-post-project').find('.ds62-progress-bar').animate({'opacity':'0'}, 400, function () {
				let percent = 0;
				if (parsedData.project_status.avaible == 0 && parsedData.project_status.progress == 0 && parsedData.project_status.check == 0 && parsedData.project_status.done == 0) {
					percent = 0;
				}else{
					percent = ((parsedData.project_status.done / (parsedData.project_status.avaible + parsedData.project_status.progress + parsedData.project_status.check + parsedData.project_status.done)).toFixed(2)) * 100;
				}
				
				if (percent == 0) {
					$(this).find('.ds62-progress-bar__title').text('Нет завершенных задач');
				}else{
					$(this).find('.ds62-progress-bar__title').text('Завершено на ' + percent + '%:');
				}
				$(this).animate({'opacity':'1'}, 400, function () {
					$(this).find('.ds62-progress-bar__line').animate({'width':percent+'%'}, 400);
				});
			});
			
		});
	}
	
	/*Функция вызова tooltip'a при наведении на автарку*/
	
	function hoverAvatar() {
		if ($(window).width() > 1000) {
			$('.ds62-user-avatar img').mouseover(function () {
				nameBlock = $(this).parents('.ds62-author-box').find('.ds62-user-display-name');
				nameBlock.addClass('ds62-user-display-name-active');
			}).mouseout( function () {
				nameBlock.removeClass('ds62-user-display-name-active');
			});
			
			$('.ds62-top-icon .elementor-widget-container').mouseover(function () {
				expiredBlock = $(this).parents('.ds62-top-icon').find('.ds62-tooltip-expired');
				expiredBlock.addClass('ds62-tooltip-expired-active');
			}).mouseout( function () {
				expiredBlock.removeClass('ds62-tooltip-expired-active');
			});
			
			$('.ds62-date-end-wrapper').mouseover(function () {
				expiredDateBlock = $(this).find('.ds62-tooltip-date-expired');
				expiredDateBlock.addClass('ds62-tooltip-expired-active');
			}).mouseout( function () {
				expiredDateBlock.removeClass('ds62-tooltip-expired-active');
			});
		}
	}
    
    /*Функция отслеживаения просрачивания даты выполнения*/
    
    function checkAndMarkerDate (column) {
		let donePosts = column.find('.ds62-section-post:not(.ds62-done)');
        let datesEnd = donePosts.find('.ds62-date-end');
        datesEnd.each(function (index, element) {
            if ($(element).text() != '--/--/----') {
                let endDateTemp = $(element).text().split('/');
                let nowDate = new Date();
                let endDate = new Date(endDateTemp[2], (endDateTemp[1] - 1), endDateTemp[0]);
                if (endDate < nowDate) {
                    $(element).parents('.ds62-section-post').find('.ds62-top-icon .elementor-widget-container').css({'background-color':'#9E0C44'});
                    $(element).parents('.ds62-date-end-wrapper').css({'color':'#9E0C44','font-weight':'600'});
					$(element).parents('.ds62-section-post').find('.ds62-top-icon').append('<div class="ds62-tooltip-expired">Задача просрочена!</div>');
					$(element).parents('.ds62-date-end-wrapper').append('<div class="ds62-tooltip-date-expired">Задача просрочена!</div>');
                }
            }
            
        });
    }

    function coloringTopIcon (column) {
        let datesEndExpired = column.find('.ds62-post-wrapper.ds62-expired');
        datesEndExpired.each(function (index, element) {
            $(element).find('.ds62-top-icon .elementor-widget-container').css({'background-color':'#9E0C44'});
            
        });
    }
      
    /*Блокировка кнопок и анимация загрузки при первой загрузке страницы*/
    
    function blockingTopButtons(buttonsToBlock) {
        $.each(buttonsToBlock, function (index, element) {
            $(element).css({'pointer-events':'none','cursor':'default'});
            $(element).find('.elementor-button-wrapper a').text('').append('<img class="ds62-loader-btn" style="height: 14px; width: 63px;" src="http://digitalstart62.ru/wp-content/themes/astra-child/img/Ellipsis-1.7s-100px1.svg"/>');
        });
    }
    
    
    /*Функция удаления контента внутри блока статусов задач*/
    
    function detachColumnsTasksChildren(columnsToDetach) {
        $.each(columnsToDetach, function (index, element) {
            $(element).children().detach();
        });
    }
    
    function refreshTaskBlocksAfterUpdateIframe() {
        /*Поиск кастомной прокрутки блоков и его удаление*/
                    
        if (ds62ColumnTasksAvaible.parents('.jspContainer').find('.jspVerticalBar').length > 0) {
            ds62ColumnTasksAvaible.parents('.jspContainer').find('.jspVerticalBar').remove();
        }
        
        if (ds62ColumnTasksProgress.parents('.jspContainer').find('.jspVerticalBar').length > 0) {
            ds62ColumnTasksProgress.parents('.jspContainer').find('.jspVerticalBar').remove();
        }
        
        if (ds62ColumnTasksCheck.parents('.jspContainer').find('.jspVerticalBar').length > 0) {
            ds62ColumnTasksCheck.parents('.jspContainer').find('.jspVerticalBar').remove();
        }
        
        if (ds62ColumnTasksDone.parents('.jspContainer').find('.jspVerticalBar').length > 0) {
            ds62ColumnTasksDone.parents('.jspContainer').find('.jspVerticalBar').remove();
        }
        
        /*Анимирование скрытия блоков и вызов функций добавления обновленного контента*/
        
        ds62ColumnTasksAvaible.animate({'opacity':'0','left':'-50%'}, 600, function () {
            ds62ColumnTasksProgress.animate({'opacity':'0','left':'-50%'}, 600);
            ds62ColumnTasksCheck.animate({'opacity':'0','left':'-50%'}, 600);
            ds62ColumnTasksDone.animate({'opacity':'0','left':'-50%'}, 600);
            detachColumnsTasksChildren([ds62ColumnTasksAvaible, ds62ColumnTasksProgress, ds62ColumnTasksCheck, ds62ColumnTasksDone]);
            showLoadingAnimation();
            
            let activeStageIdAjax = $('#ds62-stages-post-wrapper .ds62-active-post-stage').parents('div[data-elementor-type="loop"]').attr('data-post-id');
            
            getPostsTasksAvaible(activeStageIdAjax);
            getPostsTasksProgress(activeStageIdAjax);
            getPostsTasksCheck(activeStageIdAjax);
            getPostsTasksDone(activeStageIdAjax);
        });
    }
    
    /*Функция показа анимации прогрузки даееых внутри блока статусов задач*/
    
    function showLoadingAnimation() {
        $('#ds62-avaible-post-wrapper').find('.ds62-progress-animation').show();
        $('#ds62-progress-post-wrapper').find('.ds62-progress-animation').show();
        $('#ds62-check-post-wrapper').find('.ds62-progress-animation').show();
        $('#ds62-done-post-wrapper').find('.ds62-progress-animation').show();
    }
    
    /*Функция скрытия анимации прогрузки даееых внутри блока статусов задач*/
    
    function hideLoadingAnimation() {
        $('#ds62-avaible-post-wrapper').find('.ds62-progress-animation').hide();
        $('#ds62-progress-post-wrapper').find('.ds62-progress-animation').hide();
        $('#ds62-check-post-wrapper').find('.ds62-progress-animation').hide();
        $('#ds62-done-post-wrapper').find('.ds62-progress-animation').hide();
    }
    
    /*Функция добавления GET параметра в ссылку для кнопки "+ Задача" -> назначение попап модального окна для кнопки "+ Задача" -> динамическая перезагрузка блоков задач при закрытии формы */
    
    function addingIdToBtnPlusTask() {
        activeStageId = $('.ds62-active-post-stage').parents('div[data-elementor-type="loop"]').attr('data-post-id');
        
        $('#ds62-plus-task a').attr('href','/add-task?stage_id=' + activeStageId);
        let clickerCounter = 0;
        
        
        $('#ds62-plus-task a').magnificPopup({
            type: 'iframe',
            iframe: {
                markup: '<div class="mfp-iframe-scaler">'+
                '<div class="mfp-close"></div>'+
                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
            
                srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
            },
            removalDelay: 300,
            mainClass: 'mfp-fade',
            callbacks: {
                
                beforeAppend: function () {
                    this.content.find('iframe').on('load', function() {
                        let updateButton = $(this).contents().find('.wpuf-submit-button');
                        
                        updateButton.mousedown(function () {
                            updateButton.mouseup(function () {
                               clickerCounter++;
                            });
                        });
                    });
                },
                
                close: function() {
                    if (clickerCounter > 0) {
                        refreshTaskBlocksAfterUpdateIframe();
                    }
                }
            }
            
            
        });
        
        /*Поиск анимации прогрузки, если есть, то удаление и активация кнопки*/
        
        /*if ($('#ds62-plus-task .ds62-loader-btn').length > 0) {
            $('#ds62-plus-task .elementor-button-wrapper a').text('+ Задача');
            $('#ds62-plus-task .ds62-loader-btn').remove();
            $('#ds62-plus-task').css({'pointer-events':'auto','cursor':'pointer'});
        }*/
        
    }
    
    /*назначение попап модального окна для кнопки "+ Глобальная задача" -> динамическая перезагрузка блоков задач при закрытии формы */
    
    function addingIdToBtnPlusStage() {
        /*activeStageId = $('.ds62-active-post-stage').parents('div[data-elementor-type="loop"]').attr('data-post-id');
        $('#ds62-plus-task a').attr('href','/add-task?stage_id=' + activeStageId);*/
        
        let clickerCounter = 0;
        
        $('#ds62-plus-stage a').magnificPopup({
            type: 'iframe',
            iframe: {
                markup: '<div class="mfp-iframe-scaler">'+
                '<div class="mfp-close"></div>'+
                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
            
                srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
            },
            removalDelay: 300,
            mainClass: 'mfp-fade',
            callbacks: {
                
                beforeAppend: function () {
                    this.content.find('iframe').on('load', function() {
                        let updateButton = $(this).contents().find('.wpuf-submit-button');
                        
                        updateButton.mousedown(function () {
                            updateButton.mouseup(function () {
                               clickerCounter++;
                            });
                        });
                    });
                },
                
                close: function() {
                    if (clickerCounter > 0) {
                        /*Поиск кастомной прокрутки блоков и его удаление*/
                    
                        if (ds62ColumnStages.parents('.jspContainer').find('.jspVerticalBar').length > 0) {
                            ds62ColumnStages.parents('.jspContainer').find('.jspVerticalBar').remove();
                        }
                        
                        /*Анимирование скрытия блоков и вызов функций добавления обновленного контента*/
                        
                        ds62ColumnStages.animate({'opacity':'0','left':'-50%'}, 600, function () {
                            
                            ds62ColumnTasksAvaible.animate({'opacity':'0','left':'-50%'}, 600, function () {
                                
                                ds62ColumnTasksProgress.animate({'opacity':'0','left':'-50%'}, 600);
                                ds62ColumnTasksCheck.animate({'opacity':'0','left':'-50%'}, 600);
                                ds62ColumnTasksDone.animate({'opacity':'0','left':'-50%'}, 600);
                                
                                detachColumnsTasksChildren([ds62ColumnStages, ds62ColumnTasksAvaible, ds62ColumnTasksProgress, ds62ColumnTasksCheck, ds62ColumnTasksDone]);
                                
                                $('#ds62-stages-post-wrapper').find('.ds62-progress-animation').show();
                                showLoadingAnimation();
                                
                                let activeProjectIdAjax = $('#ds62-projects-post-wrapper .ds62-active-post-project').parents('div[data-elementor-type="loop"]').attr('data-post-id');
                                
                                getPostsStages(activeProjectIdAjax);
    
                            });
                        
                        });
                    }
                }
            }
            
        });
        
        /*Поиск анимации прогрузки, если есть, то удаление и активация кнопки*/
        
        /*if ($('#ds62-plus-stage .ds62-loader-btn').length > 0) {
            $('#ds62-plus-stage .elementor-button-wrapper a').text('+ Глобальная задача');
            $('#ds62-plus-stage .ds62-loader-btn').remove();
            $('#ds62-plus-stage').css({'pointer-events':'auto','cursor':'pointer'});
        }*/
        
    }
    
     /*назначение попап модального окна для кнопки "+ Проект" -> динамическая перезагрузка блоков задач при закрытии формы */
    
    function addingIdToBtnPlusProject() {
        /*activeStageId = $('.ds62-active-post-stage').parents('div[data-elementor-type="loop"]').attr('data-post-id');
        $('#ds62-plus-task a').attr('href','/add-task?stage_id=' + activeStageId);*/
        
        let clickerCounter = 0;
        
        $('#ds62-plus-project a').magnificPopup({
            type: 'iframe',
            iframe: {
                markup: '<div class="mfp-iframe-scaler">'+
                '<div class="mfp-close"></div>'+
                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
            
                srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
            },
            removalDelay: 300,
            mainClass: 'mfp-fade',
            callbacks: {
                
                beforeAppend: function () {
                    this.content.find('iframe').on('load', function() {
                        let updateButton = $(this).contents().find('.wpuf-submit-button');
                        
                        updateButton.mousedown(function () {
                            updateButton.mouseup(function () {
                               clickerCounter++;
                            });
                        });
                    });
                },
                
                close: function() {
                    if (clickerCounter > 0) {
                        if (ds62ColumnProjects.parents('.jspContainer').find('.jspVerticalBar').length > 0) {
                            ds62ColumnProjects.parents('.jspContainer').find('.jspVerticalBar').remove();
                        }
                        
                        /*Анимирование скрытия блоков и вызов функций добавления обновленного контента*/
                        
                        ds62ColumnProjects.animate({'opacity':'0'}, 600, function () {
                            
                            ds62ColumnStages.animate({'opacity':'0','left':'-50%'}, 600, function () {
                            
                                ds62ColumnTasksAvaible.animate({'opacity':'0','left':'-50%'}, 600, function () {
                                    
                                    ds62ColumnTasksProgress.animate({'opacity':'0','left':'-50%'}, 600);
                                    ds62ColumnTasksCheck.animate({'opacity':'0','left':'-50%'}, 600);
                                    ds62ColumnTasksDone.animate({'opacity':'0','left':'-50%'}, 600);
                                    
                                    detachColumnsTasksChildren([ds62ColumnProjects, ds62ColumnStages, ds62ColumnTasksAvaible, ds62ColumnTasksProgress, ds62ColumnTasksCheck, ds62ColumnTasksDone]);
                                    
                                    $('#ds62-stages-post-wrapper').find('.ds62-progress-animation').show();
                                    $('#ds62-projectss-post-wrapper').find('.ds62-progress-animation').show();
                                    //ds62ColumnProjects.find('.ds62-progress-animation').show();
                                    //ds62ColumnStages.find('.ds62-progress-animation').show();
                                    showLoadingAnimation();
                                    
                                    //let activeProjectIdAjax = $('#ds62-projects-post-wrapper .ds62-active-post-project').parents('div[data-elementor-type="loop"]').attr('data-post-id');
                                    
                                    getPostsProjects();
        
                                });
                            
                            });
                            
                        });
                    }
                }
            }
            
        });
        
        /*Поиск анимации прогрузки, если нет, то удаление и активация кнопки*/
        
        /*if ($('#ds62-plus-project .ds62-loader-btn').length > 0) {
            $('#ds62-plus-project .elementor-button-wrapper a').text('+ Проект');
            $('#ds62-plus-project .ds62-loader-btn').remove();
            $('#ds62-plus-project').css({'pointer-events':'auto','cursor':'pointer'});
        }*/
        
    }
    
    /*Функция получения комментариев в зависимости от ID активного проекта и переданного номера страницы*/
    
    function getComments(id, pnum) {
        
        let data = {
    		action: 'get_live_comments',
    		project_id: id,
    		pagenum: pnum
    	};
        
        $.post( myajax.url, data, function(response) {
            
            $('#ds62-comments').find('.ds62-progress-animation').hide();
            
            $('#ds62-live-comments').append(response);
            $('#ds62-live-comments').animate({'opacity':'1'}, 600);
            
            $('.ds62-comments').jScrollPane({
               contentWidth: '0px',
               autoReinitialise: true
            });
            
            if ($('#ds62-live-comments').find('a.page-numbers').length > 0) {

                let newPagination = '<div class="ds62-pagination">', prevElement, oldPagination;
                oldPagination = $('.page-numbers');
                $('.page-numbers').remove();
                oldPagination.each(function(index, el) {
                    if($(this).hasClass('next') || $(this).hasClass('prev')) {
                        return;
                    }else{
                        if (pnum == $(this).text()) {
                            newPagination += `<div class="ds62-page-number ds62-current-page" data-pnum="${$(this).text()}">${$(this).text()}</div>`;
                        }else{
                            newPagination += `<div class="ds62-page-number" data-pnum="${$(this).text()}">${$(this).text()}</div>`;
                        }
                    }
                });
                newPagination += "</div>";
                $('#ds62-live-comments').append(newPagination);
                $('#ds62-live-comments .ds62-page-number').click(function () {
                    
                    activeProjectId = $('#ds62-projects-post-wrapper .ds62-active-post-project').parents('div[data-elementor-type="loop"]').attr('data-post-id');
                    let pageNum = $(this).attr('data-pnum');

                    $('#ds62-live-comments').animate({'opacity':'0'}, 600, function() {
                        
                        //$('#ds62-live-comments').children().detach();
                        detachColumnsTasksChildren(['#ds62-live-comments']);
                        $('#ds62-comments').find('.ds62-progress-animation').show();
                        getComments(activeProjectId, pageNum);
                        
                    });
                    
                    $('#ds62-live-comments .ds62-page-number').off('click');
                });
            }
        
        });
    }
    
    function getPostsTasksAvaible(id) {
	    
	    let data = {
    		action: 'get_tasks_avaible',
    		stage_id: id,
    	};
	    
        $.post( myajax.url, data, function(response) {
            
            ds62ColumnTasksAvaible.append(response);
			
            
            $('#ds62-avaible-post-wrapper').jScrollPane({
                contentWidth: '0px',
                autoReinitialise: true
            });
            
            if ($('#ds62-avaible-post .elementor-posts-nothing-found').length > 0) {

                $('#ds62-avaible-post-wrapper').find('.ds62-progress-animation').hide();

                $('#ds62-avaible-post .elementor-posts-nothing-found').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Задач не найдено</span></div>');
                ds62ColumnTasksAvaible.animate({'opacity':'1','left':'0%'}, 600);
            }else{
                
                $('#ds62-avaible-post-wrapper').find('.ds62-progress-animation').hide();
                
                checkAndMarkerDate(ds62ColumnTasksAvaible);
				renderPage();
                
                ds62ColumnTasksAvaible.animate({'opacity':'1','left':'0%'}, 600, function () {
                    let ds62iconLightBox = $('#ds62-avaible-post .ds62-lightbox a');
                    let clickerCounterUpdate = 0, clickerCounterStatus = 0;
                    //checkAndMarkerDate(ds62ColumnTasksAvaible);
					hoverAvatar();
					removeNotification();
					notificationToolTip();
					
					$('.ds62-update-submit input').click(function () {
						let ds62UpdateSelect = $(this).parents('.ds62-update-wrapper').find('select').val();
						let ds62UpdatePostId = $(this).parents('.type-ds62-task').attr('data-post-id');
						console.log(ds62UpdateSelect);
						console.log(ds62UpdatePostId);
						
						let dataUpdateState = {
							action: 'task_state_change',
							task_id: ds62UpdatePostId,
							task_state: ds62UpdateSelect
						};
						
						console.log(dataUpdateState);
						
						$.post( myajax.url, dataUpdateState, function(response) {
							location.reload();
						});
					});
					
                    if (ds62iconLightBox !== null) {
                        ds62iconLightBox.magnificPopup({
                            type: 'iframe',
                            iframe: {
                                markup: '<div class="mfp-iframe-scaler">'+
                                '<div class="mfp-close"></div>'+
                                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                                '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
                            
                                srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
                            },
                            removalDelay: 300,
                            mainClass: 'mfp-fade',
                            
                            callbacks: {
                                
                                beforeAppend: function () {
                                    this.content.find('iframe').on('load', function() {
                                        let updateButton = $(this).contents().find('.wpuf-submit-button');
                                        let updateStatusButton = $(this).contents().find('input[type="submit"].acf-button');
                                        
                                        updateButton.mousedown(function () {
                                            updateButton.mouseup(function () {
                                               clickerCounterUpdate++;
                                            });
                                        });
                                        
                                        updateStatusButton.mousedown(function () {
                                            updateStatusButton.mouseup(function () {
                                               clickerCounterStatus++;
                                            });
                                        });
                                    });
                                },
                                
                                close: function() {
                                    if ((clickerCounterUpdate > 0) || (clickerCounterStatus > 0)) {
                                        refreshTaskBlocksAfterUpdateIframe();
										progressBarReloadStage(ds62StageId, ds62ProjectId);
                                    }
                                }
                                
                            }
                        });
                    }else{
                        console.log('icons not found');
                    }
                });
            }
            
        }); 
	}
	
	function getPostsTasksProgress(id) {
	    
	    let data = {
    		action: 'get_tasks_progress',
    		stage_id: id,
    	};
	    
        $.post( myajax.url, data, function(response) {

            ds62ColumnTasksProgress.append(response);
			
            
            $('#ds62-progress-post-wrapper').jScrollPane({
                contentWidth: '0px',
                autoReinitialise: true
            });

            if ($('#ds62-progress-post .elementor-posts-nothing-found').length > 0) {
                
                $('#ds62-progress-post-wrapper').find('.ds62-progress-animation').hide();
                
                $('#ds62-progress-post .elementor-posts-nothing-found').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Задач не найдено</span></div>');
                ds62ColumnTasksProgress.animate({'opacity':'1','left':'0%'}, 600);
            }else{
                
                $('#ds62-progress-post-wrapper').find('.ds62-progress-animation').hide();
                
                checkAndMarkerDate(ds62ColumnTasksProgress);
				renderPage();
                
                ds62ColumnTasksProgress.animate({'opacity':'1','left':'0%'}, 600, function () {
                    let ds62iconLightBox = $('#ds62-progress-post .ds62-lightbox a');
                    let clickerCounterUpdate = 0, clickerCounterStatus = 0;
                    //checkAndMarkerDate(ds62ColumnTasksProgress);
					hoverAvatar();
					removeNotification();
					notificationToolTip();
					
					$('.ds62-update-submit input').click(function () {
						let ds62UpdateSelect = $(this).parents('.ds62-update-wrapper').find('select').val();
						let ds62UpdatePostId = $(this).parents('.type-ds62-task').attr('data-post-id');
						console.log(ds62UpdateSelect);
						console.log(ds62UpdatePostId);
						
						let dataUpdateState = {
							action: 'task_state_change',
							task_id: ds62UpdatePostId,
							task_state: ds62UpdateSelect
						};
						
						console.log(dataUpdateState);
						
						$.post( myajax.url, dataUpdateState, function(response) {
							location.reload();
						});
					});
					
                    if (ds62iconLightBox !== null) {
                        ds62iconLightBox.magnificPopup({
                            type: 'iframe',
                            iframe: {
                                markup: '<div class="mfp-iframe-scaler">'+
                                '<div class="mfp-close"></div>'+
                                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                                '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
                            
                                srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
                            },
                            removalDelay: 300,
                            mainClass: 'mfp-fade',
                            
                            callbacks: {
                                
                                beforeAppend: function () {
                                    this.content.find('iframe').on('load', function() {
                                        let updateButton = $(this).contents().find('.wpuf-submit-button');
                                        let updateStatusButton = $(this).contents().find('input[type="submit"].acf-button');
                                        
                                        updateButton.mousedown(function () {
                                            updateButton.mouseup(function () {
                                               clickerCounterUpdate++;
                                            });
                                        });
                                        
                                        updateStatusButton.mousedown(function () {
                                            updateStatusButton.mouseup(function () {
                                               clickerCounterStatus++;
                                            });
                                        });
                                    });
                                },
                                
                                close: function() {
                                    if ((clickerCounterUpdate > 0) || (clickerCounterStatus > 0)) {
                                        refreshTaskBlocksAfterUpdateIframe();
										progressBarReloadStage(ds62StageId, ds62ProjectId);
                                    }
                                }
                                
                            }
                        });
                    }else{
                        console.log('icons not found');
                    }
                });
            }
            
        }); 
	}
	
	function getPostsTasksCheck(id) {
	    
	    let data = {
    		action: 'get_tasks_check',
    		stage_id: id,
    	};
	    
        $.post( myajax.url, data, function(response) {

            ds62ColumnTasksCheck.append(response);
			
            
            $('#ds62-check-post-wrapper').jScrollPane({
                contentWidth: '0px',
                autoReinitialise: true
            });

            if ($('#ds62-check-post .elementor-posts-nothing-found').length > 0) {
                
                $('#ds62-check-post-wrapper').find('.ds62-progress-animation').hide();
                
                $('#ds62-check-post .elementor-posts-nothing-found').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Задач не найдено</span></div>');
                ds62ColumnTasksCheck.animate({'opacity':'1','left':'0%'}, 600);
            }else{
                
                $('#ds62-check-post-wrapper').find('.ds62-progress-animation').hide();
                
                checkAndMarkerDate(ds62ColumnTasksCheck);
				renderPage();
                
                ds62ColumnTasksCheck.animate({'opacity':'1','left':'0%'}, 600, function () {
                   let ds62iconLightBox = $('#ds62-check-post .ds62-lightbox a');
                   let clickerCounterUpdate = 0, clickerCounterStatus = 0;
                   //checkAndMarkerDate(ds62ColumnTasksCheck);
				    hoverAvatar();
					removeNotification();
					notificationToolTip();
					
					$('.ds62-update-submit input').click(function () {
						let ds62UpdateSelect = $(this).parents('.ds62-update-wrapper').find('select').val();
						let ds62UpdatePostId = $(this).parents('.type-ds62-task').attr('data-post-id');
						console.log(ds62UpdateSelect);
						console.log(ds62UpdatePostId);
						
						let dataUpdateState = {
							action: 'task_state_change',
							task_id: ds62UpdatePostId,
							task_state: ds62UpdateSelect
						};
						
						console.log(dataUpdateState);
						
						$.post( myajax.url, dataUpdateState, function(response) {
							location.reload();
						});
					});
				   
                    if (ds62iconLightBox !== null) {
                        ds62iconLightBox.magnificPopup({
                            type: 'iframe',
                            iframe: {
                                markup: '<div class="mfp-iframe-scaler">'+
                                '<div class="mfp-close"></div>'+
                                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                                '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
                            
                                srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
                            },
                            removalDelay: 300,
                            mainClass: 'mfp-fade',
                            
                            callbacks: {
                                
                                beforeAppend: function () {
                                    this.content.find('iframe').on('load', function() {
                                        let updateButton = $(this).contents().find('.wpuf-submit-button');
                                        let updateStatusButton = $(this).contents().find('input[type="submit"].acf-button');
                                        
                                        updateButton.mousedown(function () {
                                            updateButton.mouseup(function () {
                                               clickerCounterUpdate++;
                                            });
                                        });
                                        
                                        updateStatusButton.mousedown(function () {
                                            updateStatusButton.mouseup(function () {
                                               clickerCounterStatus++;
                                            });
                                        });
                                    });
                                },
                                
                                close: function() {
                                    if ((clickerCounterUpdate > 0) || (clickerCounterStatus > 0)) {
                                        refreshTaskBlocksAfterUpdateIframe();
										progressBarReloadStage(ds62StageId, ds62ProjectId);
                                    }
                                }
                                
                            }
                        });
                    }else{
                        console.log('icons not found');
                    }
                });
                
            }
            
        }); 
	}
	
	function getPostsTasksDone(id) {
	    
	    let data = {
    		action: 'get_tasks_done',
    		stage_id: id,
    	};
	    
        $.post( myajax.url, data, function(response) {

            ds62ColumnTasksDone.append(response);
			
            
            $('#ds62-done-post-wrapper').jScrollPane({
                contentWidth: '0px',
                autoReinitialise: true
            });

            if ($('#ds62-done-post .elementor-posts-nothing-found').length > 0) {
                
                $('#ds62-done-post-wrapper').find('.ds62-progress-animation').hide();
                
                $('#ds62-done-post .elementor-posts-nothing-found').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Задач не найдено</span></div>');
                ds62ColumnTasksDone.animate({'opacity':'1','left':'0%'}, 600);
            }else{
                
                $('#ds62-done-post-wrapper').find('.ds62-progress-animation').hide();
				renderPage();
				
				ds62ColumnTasksDone.find('.ds62-top-icon').append('<div class="ds62-tooltip-task-done">Задача завершена</div>');
				if ($(window).width() > 1000) {
					ds62ColumnTasksDone.find('.ds62-top-icon').mouseover(function () {
						archiveBlock = $(this).find('.ds62-tooltip-task-done');
						archiveBlock.addClass('ds62-tooltip-task-done-active');
					}).mouseout( function () {
						archiveBlock.removeClass('ds62-tooltip-task-done-active');
					});
				}
				ds62ColumnTasksDone.find('.ds62-top-icon .elementor-widget-container').addClass('ds62-grey-icon-background');
				ds62ColumnTasksDone.find('.elementor-element-5e565ba .elementor-icon').addClass('ds62-grey-icon-background');
                
                ds62ColumnTasksDone.animate({'opacity':'1','left':'0%'}, 600, function () {
					let ds62iconLightBox = $('#ds62-done-post .ds62-lightbox a');
					let clickerCounterUpdate = 0, clickerCounterStatus = 0;
				   
					//checkAndMarkerDate(ds62ColumnTasksDone);
					
					hoverAvatar();
					removeNotification();
					notificationToolTip();
					
					$('.ds62-update-submit input').click(function () {
						let ds62UpdateSelect = $(this).parents('.ds62-update-wrapper').find('select').val();
						let ds62UpdatePostId = $(this).parents('.type-ds62-task').attr('data-post-id');
						console.log(ds62UpdateSelect);
						console.log(ds62UpdatePostId);
						
						let dataUpdateState = {
							action: 'task_state_change',
							task_id: ds62UpdatePostId,
							task_state: ds62UpdateSelect
						};
						
						console.log(dataUpdateState);
						
						$.post( myajax.url, dataUpdateState, function(response) {
							location.reload();
						});
					});
                   
					//checkAndMarkerDate(ds62ColumnTasksDone);
                    if (ds62iconLightBox !== null) {
                        ds62iconLightBox.magnificPopup({
                            type: 'iframe',
                            iframe: {
                                markup: '<div class="mfp-iframe-scaler">'+
                                '<div class="mfp-close"></div>'+
                                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                                '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
                            
                                srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
                            },
                            removalDelay: 300,
                            mainClass: 'mfp-fade',
                            
                            callbacks: {
                                
                                beforeAppend: function () {
                                    this.content.find('iframe').on('load', function() {
                                        let updateButton = $(this).contents().find('.wpuf-submit-button');
                                        let updateStatusButton = $(this).contents().find('input[type="submit"].acf-button');
                                        
                                        updateButton.mousedown(function () {
                                            updateButton.mouseup(function () {
                                               clickerCounterUpdate++;
                                            });
                                        });
                                        
                                        updateStatusButton.mousedown(function () {
                                            updateStatusButton.mouseup(function () {
                                               clickerCounterStatus++;
                                            });
                                        });
                                    });
                                },
                                
                                close: function() {
                                    if ((clickerCounterUpdate > 0) || (clickerCounterStatus > 0)) {
                                        refreshTaskBlocksAfterUpdateIframe();
										progressBarReloadStage(ds62StageId, ds62ProjectId);
                                    }
                                }
                                
                            }
                        });
                    }else{
                        console.log('icons not found');
                    } 
                });
            }
            
        }); 
	}

	function getPostsStages(id) {
	    
	    let data = {
    		action: 'get_stages',
    		project_id: id,
    	};
	    
        $.post( myajax.url, data, function(response) {

            $('#ds62-stages-post-wrapper').find('.ds62-progress-animation').hide();
            ds62ColumnStages.append(response);
            
            $('#ds62-stages-post-wrapper').jScrollPane({
                contentWidth: '0px',
                autoReinitialise: true
            });
            
            if ($('#ds62-stages-post .elementor-posts-nothing-found').length > 0) {
                
                $('#ds62-stages-post .elementor-posts-nothing-found').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Глобальных задач не найдено</span></div>');
                
                hideLoadingAnimation();
                
                $('#ds62-avaible-post').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Задач не найдено</span></div>');
                $('#ds62-progress-post').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Задач не найдено</span></div>');
                $('#ds62-check-post').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Задач не найдено</span></div>');
                $('#ds62-done-post').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Задач не найдено</span></div>');
                
                ds62ColumnStages.animate({'opacity':'1','left':'0%'}, 600, function () {
                    ds62ColumnTasksAvaible.animate({'opacity':'1','left':'0%'}, 600);
                    ds62ColumnTasksProgress.animate({'opacity':'1','left':'0%'}, 600);
                    ds62ColumnTasksCheck.animate({'opacity':'1','left':'0%'}, 600);
                    ds62ColumnTasksDone.animate({'opacity':'1','left':'0%'}, 600);
                    
                    addingIdToBtnPlusStage();
                });
                
            }else{
                //ds62StageId = ds62ColumnStages.find('div.type-ds62-stage').first().attr('data-post-id');
				if (ds62ColumnStages.find('.ds62-done').length > 0) {
					
					function callStagesArchiveToggle () {
						let triggerCount = false;
						$('#ds62-stages-header .elementor-icon-box-wrapper').append('<div class="ds62-stage-toggle-archive"><span class="ds62-tooltip-toggle-archive">Показать/скрыть архив</span><i class="fas fa-angle-down"></i></div>');
						$('.ds62-stage-toggle-archive').animate({'opacity':'1'}, 400);
						
						if ($(window).width() > 1000) {
							$('.ds62-stage-toggle-archive').mouseover(function () {
								toggleArchiveBlock = $(this).find('.ds62-tooltip-toggle-archive');
								toggleArchiveBlock.addClass('ds62-tooltip-toggle-archive-active');
							}).mouseout( function () {
								toggleArchiveBlock.removeClass('ds62-tooltip-toggle-archive-active');
							});
						}
						
						$('.ds62-stage-toggle-archive').click(function () {
							if (!triggerCount) {
								$(this).find('i').addClass('ds62-stage-toggle-archive-active');
								ds62ColumnStages.find('.ds62-done').addClass('ds62-done-active');
								triggerCount = true;
							}else{
								$(this).find('i').removeClass('ds62-stage-toggle-archive-active');
								ds62ColumnStages.find('.ds62-done').removeClass('ds62-done-active');
								triggerCount = false;
							}
						});
					}	
					
					if ($('.ds62-stage-toggle-archive').length > 0) {
						$('.ds62-stage-toggle-archive').animate({'opacity':'0'}, 400, function () {
							$(this).remove();
							callStagesArchiveToggle ();
						});
					}else{
						callStagesArchiveToggle ();
					}
					
				}else{
					$('.ds62-stage-toggle-archive').animate({'opacity':'0'}, 400, function () {
						$(this).remove();
					});
				}
				
				
				let ds62DoneStages = ds62ColumnStages.find('.ds62-done');
				ds62DoneStages.find('.ds62-top-icon').append('<div class="ds62-tooltip-archive">Задача в архиве</div>');
				
				if ($(window).width() > 1000) {
					ds62DoneStages.find('.ds62-top-icon').mouseover(function () {
						archiveBlock = $(this).find('.ds62-tooltip-archive');
						archiveBlock.addClass('ds62-tooltip-archive-active');
					}).mouseout( function () {
						archiveBlock.removeClass('ds62-tooltip-archive-active');
					});
				}
				
				ds62DoneStages.find('.ds62-top-icon .elementor-widget-container').css({'background-color':'#c4c4c4'});
				ds62DoneStages.find('.elementor-element-5e565ba .elementor-icon').css({'background-color':'#c4c4c4'});
				let ds62AllStages = ds62ColumnStages.find('div.type-ds62-stage .ds62-section-post:not(.ds62-done)');
                //activeStage = ds62ColumnStages.find('div.type-ds62-stage').first().find('.ds62-post-wrapper .elementor-widget-wrap');
				activeStage = ds62AllStages.first().find('.ds62-post-wrapper .elementor-widget-wrap');
				ds62StageId = activeStage.parents('div.type-ds62-stage').attr('data-post-id');
                activeStage.addClass('ds62-active-post-stage');
                
                checkAndMarkerDate(ds62ColumnStages);
				hoverAvatar();
				removeNotification();
				notificationToolTip();
                
                ds62ColumnStages.animate({'opacity':'1','left':'0%'}, 600, function () {
                    
                    addingIdToBtnPlusProject();
                    addingIdToBtnPlusStage();
                    
                    getPostsTasksAvaible(ds62StageId);
                    getPostsTasksProgress(ds62StageId);
                    getPostsTasksCheck(ds62StageId);
                    getPostsTasksDone(ds62StageId);
					
					//switcherMobile();
					
					progressBar (ds62ColumnStages, 'stage');
                    
                    let clickerCounterUpdate = 0, clickerCounterStatus = 0;
                    
                    //$('#ds62-stages-post-wrapper .ds62-post-title-btn').click(function () {
                    $('#ds62-stages-post-wrapper .ds62-post-wrapper .elementor-widget-wrap').click(function () {
                
                        activeStage.removeClass('ds62-active-post-stage');
                        
                        //blockingTopButtons(['#ds62-plus-task']);
                        
                        //activeStage = $(this).parents('.ds62-post-wrapper .elementor-widget-wrap');
                        activeStage = $(this);
                        activeStage.addClass('ds62-active-post-stage');
                        
                        ds62StageId = $(this).parents('div[data-elementor-type="loop"]').attr('data-post-id');

                        ds62ColumnTasksAvaible.animate({'opacity':'0','left':'-50%'}, 600, function () {
                            detachColumnsTasksChildren([ds62ColumnTasksAvaible, ds62ColumnTasksProgress, ds62ColumnTasksCheck, ds62ColumnTasksDone]);
                            showLoadingAnimation();
                            getPostsTasksAvaible(ds62StageId);
                            getPostsTasksProgress(ds62StageId);
                            getPostsTasksCheck(ds62StageId);
                            getPostsTasksDone(ds62StageId);
                            
                            addingIdToBtnPlusTask();
                            
                        });
                        
                    }); 
                    
                    addingIdToBtnPlusTask();
                    
                    let ds62iconLightBox = $('#ds62-stages-post .ds62-lightbox a');
                    if (ds62iconLightBox !== null) {
                        ds62iconLightBox.magnificPopup({
                            type: 'iframe',
                            iframe: {
                                markup: '<div class="mfp-iframe-scaler">'+
                                '<div class="mfp-close"></div>'+
                                '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                                '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
                            
                                srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
                            },
                            removalDelay: 300,
                            mainClass: 'mfp-fade',
                        
                            callbacks: {
                                
                                beforeAppend: function () {
                                        
                                        this.content.find('iframe').on('load', function() {
                                            let updateButton = $(this).contents().find('.wpuf-submit-button');
                                            //let header = $(this).contents().find('#ds62-header-tsp-global');
                                            //header.css({'display':'none'});
                                            
                                            //let updateStatusButton = $(this).contents().find('input[type="submit"].acf-button');
                                            
                                            updateButton.mousedown(function () {
                                                updateButton.mouseup(function () {
                                                   clickerCounterUpdate++;
                                                });
                                            });
                                            
                                            /*updateStatusButton.mousedown(function () {
                                                updateStatusButton.mouseup(function () {
                                                   clickerCounterStatus++;
                                                });
                                            });*/
                                        });
                                    
                                },
                                
                                close: function() {
                                    if (clickerCounterUpdate > 0) {
                                        //blockingTopButtons(['#ds62-plus-task','#ds62-plus-stage','#ds62-plus-project']);
                                        
                                        activeProjectId = $('#ds62-projects-post-wrapper .ds62-active-post-project').parents('div[data-elementor-type="loop"]').attr('data-post-id');
                        
                                       /*Поиск кастомной прокрутки блоков и его удаление*/
                
                                        if ($('#ds62-stages-post').parents('.jspContainer').find('.jspVerticalBar').length > 0) {
                                            $('#ds62-stages-post').parents('.jspContainer').find('.jspVerticalBar').remove();
                                        }
                                        
                                        /*Анимирование скрытия блоков и вызов функций добавления обновленного контента*/
                                        
                                        ds62ColumnStages.animate({'opacity':'0','left':'-50%'}, 600, function () {
                                            
                                            ds62ColumnTasksAvaible.animate({'opacity':'0','left':'-50%'}, 600, function () {
                                                
                                                ds62ColumnTasksProgress.animate({'opacity':'0','left':'-50%'}, 600);
                                                ds62ColumnTasksCheck.animate({'opacity':'0','left':'-50%'}, 600);
                                                ds62ColumnTasksDone.animate({'opacity':'0','left':'-50%'}, 600);
                                                
                                                detachColumnsTasksChildren([ds62ColumnStages, ds62ColumnTasksAvaible, ds62ColumnTasksProgress, ds62ColumnTasksCheck, ds62ColumnTasksDone]);
                                                
                                                $('#ds62-stages-post-wrapper').find('.ds62-progress-animation').show();
                                                showLoadingAnimation();
                                                
                                                let activeProjectIdAjax = $('#ds62-projects-post-wrapper .ds62-active-post-project').parents('div[data-elementor-type="loop"]').attr('data-post-id');
                                                
                                                getPostsStages(ds62ProjectId);
                    
                                            });
                                        
                                        });
                                    }
                                }
                            }
                        });
                    }else{
                        console.log('icons not found');
                    }
                });
            }
            
            
        }); 
	}
	
	function getPostsProjects() {
	    
	    let data = {
    		action: 'get_projects'
    	};
    	
    	$.post( myajax.url, data, function(response) {

            $('#ds62-projects-post-wrapper').find('.ds62-progress-animation').hide();
            ds62ColumnProjects.append(response);
            
            //ds62ProjectId = $('#ds62-projects div.type-ds62-project').first().attr('data-post-id');
            
            if ($('#ds62-projects-post .elementor-posts-nothing-found').length > 0) {
                
                $('#ds62-projects-post .elementor-posts-nothing-found').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Проекты не найдены</span></div>');
                
                $('#ds62-stages-post-wrapper').find('.ds62-progress-animation').hide();
                hideLoadingAnimation();
                
                $('#ds62-stages-post').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Глобальных задач не найдено</span></div>');
                $('#ds62-avaible-post').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Задач не найдено</span></div>');
                $('#ds62-progress-post').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Задач не найдено</span></div>');
                $('#ds62-check-post').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Задач не найдено</span></div>');
                $('#ds62-done-post').append('<div class="ds62-clear-task-stage"><div class="ds62-clear-task-stage__icon"><i aria-hidden="true" class="fas fa-check"></i></div><span>Задач не найдено</span></div>');
                
                ds62ColumnProjects.animate({'opacity':'1'}, 600, function () {
                    
                    $('#ds62-stages-post').animate({'opacity':'1','left':'0%'}, 600);
                    $('#ds62-avaible-post').animate({'opacity':'1','left':'0%'}, 600);
                    $('#ds62-progress-post').animate({'opacity':'1','left':'0%'}, 600);
                    $('#ds62-check-post').animate({'opacity':'1','left':'0%'}, 600);
                    $('#ds62-done-post').animate({'opacity':'1','left':'0%'}, 600);
                    
                    addingIdToBtnPlusProject();
                    
                });
                
            }else{
				
				if (ds62ColumnProjects.find('.ds62-done').length > 0) {
					
					let triggerCount = false;
					$('#ds62-projects-header .elementor-icon-box-wrapper').append('<div class="ds62-project-toggle-archive"><span class="ds62-tooltip-toggle-archive ds62-tooltip-toggle-archive_p">Показать/скрыть архив</span><i class="fas fa-angle-down"></i></div>');
					$('.ds62-project-toggle-archive').animate({'opacity':'1'}, 400);
					
					if ($(window).width() > 1000) {
						$('.ds62-project-toggle-archive').mouseover(function () {
							toggleArchiveBlock = $(this).find('.ds62-tooltip-toggle-archive');
							toggleArchiveBlock.addClass('ds62-tooltip-toggle-archive-active');
						}).mouseout( function () {
							toggleArchiveBlock.removeClass('ds62-tooltip-toggle-archive-active');
						});
					}
					
					$('.ds62-project-toggle-archive').click(function () {
						if (!triggerCount) {
							$(this).find('i').addClass('ds62-project-toggle-archive-active');
							ds62ColumnProjects.find('.ds62-done').addClass('ds62-done-active');
							triggerCount = true;
						}else{
							$(this).find('i').removeClass('ds62-project-toggle-archive-active');
							ds62ColumnProjects.find('.ds62-done').removeClass('ds62-done-active');
							triggerCount = false;
						}
					});
					
				}else{
					$('.ds62-project-toggle-archive').animate({'opacity':'0'}, 400, function () {
						$(this).remove();
					});
				}
				
				
				let ds62DoneProjects = ds62ColumnProjects.find('.ds62-done');
				ds62DoneProjects.find('.ds62-top-icon').append('<div class="ds62-tooltip-archive">Задача в архиве</div>');
				
				if ($(window).width() > 1000) {
					ds62DoneProjects.find('.ds62-top-icon').mouseover(function () {
						archiveBlock = $(this).find('.ds62-tooltip-archive');
						archiveBlock.addClass('ds62-tooltip-archive-active');
					}).mouseout( function () {
						archiveBlock.removeClass('ds62-tooltip-archive-active');
					});
				}
				
				ds62DoneProjects.find('.ds62-top-icon .elementor-widget-container').css({'background-color':'#c4c4c4'});
				ds62DoneProjects.find('.elementor-element-5e565ba .elementor-icon').css({'background-color':'#c4c4c4'});
				let ds62UndoneProjects = ds62ColumnProjects.find('div.type-ds62-project .ds62-section-post:not(.ds62-done)');
                //activeStage = ds62ColumnStages.find('div.type-ds62-stage').first().find('.ds62-post-wrapper .elementor-widget-wrap');
				activeProject = ds62UndoneProjects.first().find('.ds62-post-wrapper .elementor-widget-wrap');
				ds62ProjectId = activeProject.parents('div.type-ds62-project').attr('data-post-id');
                
                let activeProjectIdAjax = $('#ds62-projects-post-wrapper .ds62-active-post-project').parents('div[data-elementor-type="loop"]').attr('data-post-id');				
                
                //activeProject = ds62ColumnProjects.find('div.type-ds62-project').first().find('.ds62-post-wrapper .elementor-widget-wrap');
                activeProject.addClass('ds62-active-post-project');
                
                $('#ds62-projects-post-wrapper').jScrollPane({
                   contentWidth: '0px',
                   autoReinitialise: true
                });
                
                checkAndMarkerDate(ds62ColumnProjects);
				hoverAvatar();
				removeNotification();
				notificationToolTip();
                
                ds62ColumnProjects.animate({'opacity':'1'}, 600, function () {
                    
                    getPostsStages(ds62ProjectId);
                    
                    addingIdToBtnPlusProject();
                    
                    let clickerCounterUpdate = 0;
					
					progressBar (ds62ColumnProjects, 'project');
                    
                    $('#ds62-projects .ds62-lightbox a').magnificPopup({
                        type: 'iframe',
                        iframe: {
                            markup: '<div class="mfp-iframe-scaler">'+
                            '<div class="mfp-close"></div>'+
                            '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
                            '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
                        
                            srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
                        },
                        removalDelay: 300,
                        mainClass: 'mfp-fade',
                        
                        callbacks: {
                            
                            beforeAppend: function () {
                                this.content.find('iframe').on('load', function() {
                                    let updateButton = $(this).contents().find('.wpuf-submit-button');
                                    //let updateStatusButton = $(this).contents().find('input[type="submit"].acf-button');
                                    
                                    updateButton.mousedown(function () {
                                        updateButton.mouseup(function () {
                                           clickerCounterUpdate++;
                                        });
                                    });
                                    
                                    /*updateStatusButton.mousedown(function () {
                                        updateStatusButton.mouseup(function () {
                                           clickerCounterStatus++;
                                        });
                                    });*/
                                });
                            },
                            
                            close: function() {
                                if (clickerCounterUpdate > 0) {
                                    //blockingTopButtons(['#ds62-plus-task','#ds62-plus-stage','#ds62-plus-project']);
                                    $('#ds62-projects-post').animate({'opacity':'0'}, 600, function () {
                    
                                        ds62ColumnStages.animate({'opacity':'0','left':'-50%'}, 600, function () {
                                        
                                            ds62ColumnTasksAvaible.animate({'opacity':'0','left':'-50%'}, 600, function () {
                                                
                                                ds62ColumnTasksProgress.animate({'opacity':'0','left':'-50%'}, 600);
                                                ds62ColumnTasksCheck.animate({'opacity':'0','left':'-50%'}, 600);
                                                ds62ColumnTasksDone.animate({'opacity':'0','left':'-50%'}, 600);
                                                
                                                detachColumnsTasksChildren([ds62ColumnProjects, ds62ColumnStages, ds62ColumnTasksAvaible, ds62ColumnTasksProgress, ds62ColumnTasksCheck, ds62ColumnTasksDone]);
                                                
                                                $('#ds62-projects-post-wrapper').find('.ds62-progress-animation').show();
                                                $('#ds62-stages-post-wrapper').find('.ds62-progress-animation').show();
                                                showLoadingAnimation();
                                                
                                                getPostsProjects();
                    
                                            });
                                        
                                        });
                                        
                                    });
                                }
                            }
                        }
                    });
                });
                
                if ($('#ds62-live-comments').children().length > 0) {
                    $('#ds62-live-comments').animate({'opacity':'0'}, 600, function () {
                        detachColumnsTasksChildren(['#ds62-live-comments']);
                        $('#ds62-comments').find('.ds62-progress-animation').show();
                        getComments(ds62ProjectId, 1);
                    });
                }else{
                    getComments(ds62ProjectId, 1);
                }
                
                //$('#ds62-projects .ds62-post-title-btn').click(function () {
                $('#ds62-projects .ds62-post-wrapper .elementor-widget-wrap').click(function () {
					
					$('.ds62-stage-toggle-archive').animate({'opacity':'0'}, 400, function () {
						$(this).remove();
					});
                    
                    activeProject.removeClass('ds62-active-post-project');
                    
                    //blockingTopButtons(['#ds62-plus-task','#ds62-plus-stage']);
                    
                    //activeProject = $(this).parents('.ds62-post-wrapper .elementor-widget-wrap');
                    activeProject = $(this);
                    activeProject.addClass('ds62-active-post-project');
                    
                    //ds62ProjectId = $(this).parents('div[data-elementor-type="loop"]').attr('data-post-id');
                    ds62ProjectId = $('#ds62-projects-post-wrapper .ds62-active-post-project').parents('div[data-elementor-type="loop"]').attr('data-post-id');
                    ds62ColumnTasksAvaible.animate({'opacity':'0','left':'-50%'}, 600, function () {
                        ds62ColumnStages.animate({'opacity':'0','left':'-50%'}, 600, function () {
                            
                            detachColumnsTasksChildren([ds62ColumnStages, ds62ColumnTasksAvaible, ds62ColumnTasksProgress, ds62ColumnTasksCheck, ds62ColumnTasksDone]);
                            $('#ds62-stages-post-wrapper').find('.ds62-progress-animation').show();
                            showLoadingAnimation();
                            
                            getPostsStages(ds62ProjectId);
							//switcherMobile();
                            if ($('#ds62-live-comments').children().length > 0) {
                                $('#ds62-live-comments').animate({'opacity':'0'}, 600, function () {
                                    detachColumnsTasksChildren(['#ds62-live-comments']);
                                    $('#ds62-comments').find('.ds62-progress-animation').show();
                                    getComments(ds62ProjectId, 1);
                                });
                            }else{
                                getComments(ds62ProjectId, 1);
                            }
                            
                        });
                    });
                    
                });
                
            }
    	});
	    
	    
	}
	
	//blockingTopButtons(['#ds62-plus-task','#ds62-plus-stage','#ds62-plus-project']);
	getPostsProjects();
	switcherMobile();
	//renderPage();
});