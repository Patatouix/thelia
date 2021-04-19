$(function($){
    // Manage document upload
    $.documentUploadManager = {};

    Dropzone.autoDiscover = false;



    // Remove document on click
    $.documentUploadManager.initDocumentDropZone = function() {
        $.documentUploadManager.onClickDeleteDocument();
        $.documentUploadManager.onClickModal();
        $.documentUploadManager.onModalHidden();
        $.documentUploadManager.sortDocument();
        $.documentUploadManager.onClickToggleVisibilityDocument();

        //initiate dropzone

        let documentDropzone = document.getElementById('documents-dropzone');
        let fileInput = documentDropzone.querySelector('input[type=file]');

        //for browsers that enable JS, hide fallback and show upload button
        documentDropzone.querySelector('.fallback').classList.add('d-none');
        documentDropzone.querySelector('.btn-browse').classList.remove('d-none');

        documentDropzone.addEventListener('click', () => fileInput.click());

        //enable droppable zone
        const preventDefaults = (e) => {
            e.preventDefault();
            e.stopPropagation();
        };

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            documentDropzone.addEventListener(eventName, preventDefaults, false)
        });

        //highlight dropzone on hover
        const highlight = (e) => {
            documentDropzone.classList.add('dz-drag-hover');
        };

        const unhighlight = (e) => {
            documentDropzone.classList.remove('dz-drag-hover');
        };

        ['dragenter', 'dragover'].forEach(eventName => {
            documentDropzone.addEventListener(eventName, highlight, false)
        });

        ['dragleave', 'drop'].forEach(eventName => {
            documentDropzone.addEventListener(eventName, unhighlight, false)
        });

        //file drop event
        const handleDrop = (e) => {
            let files = e.dataTransfer.files;
            Array.from(files).forEach((file) => {
                uploadFile(file);
            });
        };

        documentDropzone.addEventListener('drop', handleDrop, false);

        //do the same when uploading files
        fileInput.addEventListener('change', (e) => {
            let files = fileInput.files;
            Array.from(files).forEach((file) => {
                uploadFile(file);
            });
        });

        //send dropped files to the server
        const uploadFile = (file) => {
            var url = documentDropzone.getAttribute('action');
            var xhr = new XMLHttpRequest();
            var formData = new FormData();
            xhr.open('POST', url, true);

            xhr.addEventListener('readystatechange', (e) => {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    //documentDropzone.removeFile(file);
                    $.documentUploadManager.updateDocumentListAjax();
                    $.documentUploadManager.onClickDeleteDocument();
                    $.documentUploadManager.onClickToggleVisibilityDocument();
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
            reader.onloadend = () => {

                let template = '<div class="dz-preview dz-file-preview dz-error">'
                    + '<div class="dz-details">'
                        + '<div class="dz-filename">'
                            + '<span data-dz-name="">' + file.name + '</span>'
                        + '</div>'
                        + '<div class="dz-size" data-dz-size="">'
                            + '<strong>' + file.size / 1000000 + '</strong> MB'
                        + '</div>'
                        + (file.type.startsWith('image/') ? ('<img data-dz-thumbnail="" src="' + reader.result + '" alt="' + file.name + '">') : '')
                    + '</div>'
                    +  '<div class="dz-error-mark"><span>✘</span></div>'
                    + '<div class="dz-error-message"><span data-dz-errormessage="">' + error + '</span></div>'
                + '</div>';

                documentDropzone.querySelector('#documentPreview').insertAdjacentHTML('beforeend', template);
            }
        }
    };

    // Update picture list via AJAX call
    $.documentUploadManager.updateDocumentListAjax = function() {
        var $documentListArea = $(".document-manager .existing-document");
        $documentListArea.html('<div class="loading" ></div>');
        $.ajax({
            type: "POST",
            url: documentListUrl,
            statusCode: {
                404: function() {
                    $documentListArea.html(
                        documentListErrorMessage
                    );
                }
            }
        }).done(function(data) {
            $documentListArea.html(
                data
            );
            $.documentUploadManager.onClickDeleteDocument();
            $.documentUploadManager.sortDocument();
            $.documentUploadManager.onClickToggleVisibilityDocument();
        });
    };

    // Remove document on click
    $.documentUploadManager.onClickDeleteDocument = function() {
        $('.document-manager .document-delete-btn').on('click', function (e) {
            e.preventDefault();
            $("#submit-delete-document").data("element-id", $(this).attr("id"));
            $('#document_delete_dialog').modal("show");

            return false;
        });
    };

    $.documentUploadManager.onModalHidden = function() {
        $("#document_delete_dialog").on('hidden.bs.modal', function (e) {
            $("#submit-delete-document").data("element-id", "");
        });
    };

    $.documentUploadManager.onClickModal = function() {
        $("#submit-delete-document").on('click', function(e){

            var $id= $(this).data("element-id");
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
                        $(".document-manager .message").html(
                            errorMessage
                        );
                    }
                }
            }).done(function(data) {
                $greatParent.remove();
                $(".document-manager .message").html(
                    data
                );

                /* refresh position */
                $( "#js-sort-document").children('li').each(function(position, element) {
                    $(element).find('.js-sorted-position').html(position + 1);
                });
            }).always(function() {
                $('#document_delete_dialog').modal("hide");
                $("#submit-delete-document").data("element-id", "");
            });
        });
    };

    // toggle document on click
    $.documentUploadManager.onClickToggleVisibilityDocument = function() {
        $('.document-manager').on('click', '.document-toggle-btn', function (e) {
            e.preventDefault();
            var $this = $(this);
            //$parent.append('<div class="loading" ></div>');
            var $url = $this.attr("href");
            var errorMessage = $this.attr("data-error-message");
            $.ajax({
                type: "GET",
                url: $url,
                statusCode: {
                    404: function() {
                        $(".document-manager .message").html(
                            errorMessage
                        );
                    }
                }
            }).done(function(data) {
                $(".document-manager .message").html(
                    data
                );

                $this.toggleClass("visibility-visible");
            });
            return false;
        });
    };

    $.documentUploadManager.sortDocument = function() {

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

            //prevents from dragging wrong children elements
            item.querySelectorAll('a').forEach(el => el.setAttribute('draggable', false));

            item.addEventListener('dragstart', (event) => {
                //change cursor during drag
                event.dataTransfer.effectAllowed  = 'move';
            });

            item.addEventListener('drag', (event) => {
                //indicates that dropzone is not droppable for this purpose
                document.querySelector('#documents-dropzone').style.pointerEvents = 'none';

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
                document.querySelector('#documents-dropzone').style.pointerEvents = 'auto';

                const selectedItem = event.target;
                selectedItem.style.opacity = 1;

                //send position to server
                const newPosition = selectedItem.querySelector('.js-sorted-position').innerHTML;
                const documentId = selectedItem.dataset.sortId;
                $.ajax({
                    type: "POST",
                    url: documentReorder,
                    data: {
                        document_id: documentId,
                        position: newPosition
                    },
                    statusCode: {
                        404: function() {
                            $(".document-manager .message").html(
                                documentReorderErrorMessage
                            );
                        }
                    }
                }).done(function(data) {
                    $(".document-manager .message").html(
                        data
                    );
                });
            });
        }

        enableDragSort(document.querySelectorAll('#js-sort-document'));
    }
});
