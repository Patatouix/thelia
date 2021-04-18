$(function($){
    // Manage picture upload
    $.imageUploadManager = {};

    Dropzone.autoDiscover = false;

    // Remove image on click
    $.imageUploadManager.initImageDropZone = function() {

        $.imageUploadManager.onClickDeleteImage();
        $.imageUploadManager.onClickModal();
        $.imageUploadManager.onModalHidden();
        //$.imageUploadManager.sortImage();
        $.imageUploadManager.testSortable();
        $.imageUploadManager.onClickToggleVisibilityImage();
        $.imageUploadManager.onClickBtnDeleteSelectedImages();
        $.imageUploadManager.onClickBtnSelectDeselectImages();

        //////////////////////////////////////////

        let imageDropzone = document.getElementById('images-dropzone');
        let fileInput = imageDropzone.querySelector('input[type=file]');

        //for browsers that enable JS, hide fallback and show upload button
        imageDropzone.querySelector('.fallback').classList.add('d-none');
        imageDropzone.querySelector('.btn-browse').classList.remove('d-none');

        imageDropzone.addEventListener('click', () => fileInput.click());

        //enable droppable zone
        const preventDefaults = (e) => {
            e.preventDefault();
            e.stopPropagation();
        };

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            imageDropzone.addEventListener(eventName, preventDefaults, false)
        });

        //highlight dropzone on hover
        const highlight = (e) => {
            imageDropzone.classList.add('dz-drag-hover');
        };

        const unhighlight = (e) => {
            imageDropzone.classList.remove('dz-drag-hover');
        };

        ['dragenter', 'dragover'].forEach(eventName => {
            imageDropzone.addEventListener(eventName, highlight, false)
        });

        ['dragleave', 'drop'].forEach(eventName => {
            imageDropzone.addEventListener(eventName, unhighlight, false)
        });

        //file drop event
        const handleDrop = (e) => {
            let files = e.dataTransfer.files;
            Array.from(files).forEach((file) => {
                uploadFile(file);
            });
        };

        imageDropzone.addEventListener('drop', handleDrop, false);

        //do the same when uploading files
        fileInput.addEventListener('change', (e) => {
            let files = fileInput.files;
            Array.from(files).forEach((file) => {
                uploadFile(file);
            });
        });

        //send dropped files to the server
        const uploadFile = (file) => {
            var url = imageDropzone.getAttribute('action');
            var xhr = new XMLHttpRequest();
            var formData = new FormData();
            xhr.open('POST', url, true);

            xhr.addEventListener('readystatechange', (e) => {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    //imageDropzone.removeFile(file);
                    $.imageUploadManager.updateImageListAjax();
                    $.imageUploadManager.onClickDeleteImage();
                    $.imageUploadManager.onClickToggleVisibilityImage();
                }
                else if (xhr.readyState == 4 && xhr.status != 200) {
                    previewFile(file, xhr.responseText);
                }
            });

            formData.append('file', file);
            xhr.send(formData);
        }

        //thumbnail of dropped files
        const previewFile = (file, error) => {
            let reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = () => { console.log(file);

                let template = '<div class="dz-preview dz-file-preview dz-error">'
                    + '<div class="dz-details">'
                        + '<div class="dz-filename">'
                            + '<span data-dz-name="">' + file.name + '</span>'
                        + '</div>'
                        + '<div class="dz-size" data-dz-size="">'
                            + '<strong>' + file.size / 1000000 + '</strong> MB'
                        + '</div>'
                        + '<img data-dz-thumbnail="" src="' + reader.result + '" alt="' + file.name + '">'
                    + '</div>'
                    +  '<div class="dz-error-mark"><span>✘</span></div>'
                    + '<div class="dz-error-message"><span data-dz-errormessage="">' + error + '</span></div>'
                + '</div>';

                imageDropzone.querySelector('#gallery').insertAdjacentHTML('beforeend', template);
            }
        }

        //////////////////////////////////////////

        /*var imageDropzone = new Dropzone("#images-dropzone", {
            dictDefaultMessage : $('.btn-browse').html(),
            uploadMultiple: false,
            acceptedFiles: 'image/png, image/gif, image/jpeg'
        });*/

        /*var totalFiles      = 0,
            completedFiles  = 0;

        imageDropzone.on("addedfile", function(file){
            totalFiles += 1;

            if(totalFiles == 1){
                $('.dz-message').hide();
            }
        });

        imageDropzone.on("complete", function(file){
            completedFiles += 1;

            if (completedFiles === totalFiles){
                $('.dz-message').slideDown();
            }
        });*/

        /*imageDropzone.on("success", function(file) {
            imageDropzone.removeFile(file);
            $.imageUploadManager.updateImageListAjax();
            $.imageUploadManager.onClickDeleteImage();
            $.imageUploadManager.onClickToggleVisibilityImage();
        });*/
    };

    // Update picture list via AJAX call
    $.imageUploadManager.updateImageListAjax = function() {
        var $imageListArea = $(".image-manager .existing-image");
        $imageListArea.html('<div class="loading" ></div>');
        $.ajax({
            type: "POST",
            url: imageListUrl,
            data: {
                successUrl: imageSuccessUrl
            },
            statusCode: {
                404: function() {
                    $imageListArea.html(
                        imageListErrorMessage
                    );
                }
            }
        }).done(function(data) {
                $imageListArea.html(
                    data
                );
                $.imageUploadManager.onClickDeleteImage();
                $.imageUploadManager.sortImage();
                $.imageUploadManager.onClickToggleVisibilityImage();
            });
    };

    // Remove image on click
    $.imageUploadManager.onClickDeleteImage = function() {
        $('.image-manager .image-delete-btn').on('click', function (e) {
            e.preventDefault();
            var $btnSubmit =  $("#submit-delete-image");
            $btnSubmit.data("element-id", $(this).attr("id"));
            $btnSubmit.data("type", "one");
            $('#modal-body-delete-image').html(imageDeleteOneWarningMessage);
            $('#image_delete_dialog').modal("show");

            return false;
        });
    };

    $.imageUploadManager.onClickBtnDeleteSelectedImages = function(){
        $('.btn-delete-selected-images').on('click', function(e) {
            e.preventDefault();
            var $btnSubmit = $("#submit-delete-image");
            var $btnCancel = $("#jsBtnDismissImageDeleteModal");
            $btnSubmit.data("type", "multiple");
            var $count = $('.image-select-checkbox:checked').length;
            var $warning;
            if($count == 0){
                $btnSubmit.hide();
                $btnCancel.html(textBtnDeleteImageModal_Close);
                $warning = imageDeleteNoImageSelectedMessage;
            }else{
                $btnSubmit.show();
                $btnCancel.html('<span class="fas fa-times"></span>'+textBtnDeleteImageModal_No);
                if($count == 1){
                    $warning = imageDeleteOneWarningMessage;
                } else {
                    $warning = imageDeleteMultipleWarningMessage.replace("%count", $count);
                }
            }
            $('#modal-body-delete-image').html($warning);
            $('#image_delete_dialog').modal("show");

            return false;
        });
    };

    $.imageUploadManager.onClickBtnSelectDeselectImages = function(){
        $('.btn-select-all-images').on('click', function(e) {
            e.preventDefault();
            $('.image-select-checkbox').prop('checked', true);
        });

        $('.btn-deselect-all-images').on('click', function(e) {
            e.preventDefault();
            $('.image-select-checkbox').prop('checked', false);
        });
    };

    $.imageUploadManager.onModalHidden = function() {
        $("#image_delete_dialog").on('hidden.bs.modal', function (e) {
            var $btnSubmit =  $("#submit-delete-image");
            $btnSubmit.data("element-id", "");
            $btnSubmit.data("type", "");
            $('#modal-body-delete-image').html('');
        });
    };

    $.imageUploadManager.deleteSelectedImages = function(){
        $('.image-select-checkbox:checked').each(function(){
            $.imageUploadManager.deleteImage($(this).data("id"));
        });
    };

    $.imageUploadManager.deleteImage = function($id){
        var $this = $("#"+$id);
        var $parent = $this.parent();
        var $greatParent = $parent.parent();

        $greatParent.append('<div class="loading" ></div>');
        $greatParent.find('.btn-group').remove();
        var $url = $this.attr("href");
        var errorMessage = $this.attr("data-error-message");
        $.ajax({
            type: "POST",
            url: $url,
            statusCode: {
                404: function() {
                    $(".image-manager .message").html(
                        errorMessage
                    );
                }
            }
        }).done(function(data) {
            $greatParent.parent().remove();
            $(".image-manager .message").html(
                data
            );

            /* refresh position */
            $( "#js-sort-image").children('li').each(function(position, element) {
                $(element).find('.js-sorted-position').html(position + 1);
            });
        }).always(function(){
            $('#image_delete_dialog').modal("hide");
        });
    };

    $.imageUploadManager.onClickModal = function() {
        $("#submit-delete-image").on('click', function(e){
            var $type = $(this).data("type");
            if($type == 'one') {
                var $id = $(this).data("element-id");
                $.imageUploadManager.deleteImage($id);
            }else if($type == 'multiple'){
                $.imageUploadManager.deleteSelectedImages();
            }
        });
    };

    // toggle document on click
    $.imageUploadManager.onClickToggleVisibilityImage = function() {
        $('.image-manager').on('click', '.image-toggle-btn', function (e) {
            e.preventDefault();
            var $this = $(this);
            var $url = $this.attr("href");
            var errorMessage = $this.attr("data-error-message");
            $.ajax({
                type: "GET",
                url: $url,
                statusCode: {
                    404: function() {
                        $(".image-manager .message").html(
                            errorMessage
                        );
                    }
                }
            }).done(function(data) {
                $(".image-manager .message").html(
                    data
                );

                $this.toggleClass("visibility-visible");
            });
            return false;
        });
    };

    $.imageUploadManager.sortImage = function() {
        $( "#js-sort-image" ).sortable({
            placeholder: "ui-sortable-placeholder col-sm-6 col-md-3",
            change: function( event, ui ) {
                /* refresh position */
                var pickedElement = ui.item;
                var position = 0;
                $( "#js-sort-image").children('li').each(function(k, element) {
                    if($(element).data('sort-id') == pickedElement.data('sort-id')) {
                        return true;
                    }
                    position++;
                    if($(element).is('.ui-sortable-placeholder')) {
                        pickedElement.find('.js-sorted-position').html(position);
                    } else {
                        $(element).find('.js-sorted-position').html(position);
                    }
                });
            },
            stop: function( event, ui ) {
                /* update */
                var newPosition = ui.item.find('.js-sorted-position').html();
                var imageId = ui.item.data('sort-id');

                $.ajax({
                    type: "POST",
                    url: imageReorder,
                    data: {
                        image_id: imageId,
                        position: newPosition
                    },
                    statusCode: {
                        404: function() {
                            $(".image-manager .message").html(
                                imageReorderErrorMessage
                            );
                        }
                    }
                }).done(function(data) {
                    $(".image-manager .message").html(
                        data
                    );
                });
            }
        });
        $( "#js-sort-image" ).disableSelection();
    };


    $.imageUploadManager.testSortable = function() {

        function enableDragSort(draggableLists) {
            draggableLists.forEach((list) => {
                enableDragList(list);
            })
        }

        function enableDragList(list) {
            list.addEventListener('drop', (e) => e.preventDefault());
            list.addEventListener('dragover', (e) => e.preventDefault());
            Array.from(list.children).forEach((item) => {
                enableDragItem(item);
            })
        }

        function enableDragItem(item) {

            item.setAttribute('draggable', true);

            //prevents from dragging wrong elements
            item.querySelector('a.thumbnail').setAttribute('draggable', false);
            item.querySelector('img.card-img').setAttribute('draggable', false);

            item.addEventListener('dragstart', (event) => {
                //change cursor during drag
                event.dataTransfer.effectAllowed  = 'move';
            });

            item.addEventListener('drag', (event) => {
                //indicates that dropzone is not droppable for this purpose
                document.querySelector('#images-dropzone').style.pointerEvents = 'none';

                const selectedItem = event.target,
                    list = selectedItem.parentNode,
                    x = event.clientX,
                    y = event.clientY;

                selectedItem.style.opacity = 0;

                let swapItem = document.elementFromPoint(x, y) === null ? selectedItem : document.elementFromPoint(x, y);

                if (list === swapItem.parentNode) {
                    nextItem = swapItem !== selectedItem.nextSibling ? swapItem : swapItem.nextSibling;
                    list.insertBefore(selectedItem, nextItem);

                    //refresh position
                    Array.from(list.children).forEach((item) => {
                        item.querySelector('.js-sorted-position').innerHTML = Array.prototype.indexOf.call(list.children, item) + 1;
                    });
                }
            });

            item.addEventListener('dragend', (event) => {
                //make dropzone droppable again
                document.querySelector('#images-dropzone').style.pointerEvents = 'auto';

                const selectedItem = event.target;
                selectedItem.style.opacity = 1;

                //send position to server
                const newPosition = selectedItem.querySelector('.js-sorted-position').innerHTML;
                const imageId = selectedItem.dataset.sortId;
                $.ajax({
                    type: "POST",
                    url: imageReorder,
                    data: {
                        image_id: imageId,
                        position: newPosition
                    },
                    statusCode: {
                        404: function() {
                            $(".image-manager .message").html(
                                imageReorderErrorMessage
                            );
                        }
                    }
                }).done(function(data) {
                    $(".image-manager .message").html(
                        data
                    );
                });
            });
        }

        enableDragSort(document.querySelectorAll('#js-sort-image'));
    }
});