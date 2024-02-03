<?php
$name = isset($name) ? $name : "post_content";
$value = isset($value) ? $value : "";
$id = isset($id) ? $id : "post-content";
?>

<div style="margin-bottom:5px;">
    <a id="add_files" class="btn btn-default" href="#">
        <i class="btn-label icon fa fa-camera"></i>
        &nbsp;{!!Lang::get('admin::editor.add_media')!!}</a>
    <a id="add_files" class="btn btn-default btn-md pull-right translator-btn" href="#">
        <i class="fa fa-microphone" aria-hidden="true"></i>
    </a>
</div>

<textarea name="<?php echo $name; ?>" id="<?php echo $id; ?>"><?php echo $value; ?></textarea>

@push("footer")

    <script type="text/javascript" src="<?php echo assets('admin::ckeditor/ckeditor.js'); ?>"></script>
    <script src="<?php echo assets('admin::js/voice.js') ?>"></script>
    <script>

        $(document).ready(function () {

            $(".translator-btn").click(function () {
                translate();
            });

            function parseHtmlEnteties(str) {
                str = str.replace("&nbsp;", "");
                return str.replace(/&#([0-9]{1,3});/gi, function (match, numStr) {
                    var num = parseInt(numStr, 10); // read num as normal number
                    return String.fromCharCode(num);
                });
            }

            function translate() {

                var objEditor = CKEDITOR.instances["<?php echo $id; ?>"];
                var msg = objEditor.getData().replace(/<\/?[^>]+(>|$)/g, "");

                responsiveVoice.speak(parseHtmlEnteties(msg), "Arabic Male");
            }
        });

        CKEDITOR.replace("<?php echo $id; ?>", {
            language: '<?php echo App::getLocale(); ?>',
            height: '300px',
            resize_enabled: true,
            resize_dir: 'vertical',
        });

        $(document).ready(function () {

            $("#add_files").filemanager({
                types: "image|video|audio|pdf",
                done: function (files) {
                    if (files.length) {
                        files.forEach(function (file) {
                            var html = "";
                            if (file.url.split('.').pop() == 'pdf') {
                                html += "<iframe src='" + file.url + "' width='100%' height='300px'  /><br>";
                                html += "<a href='" + file.url + "' target='_blank'><img src='<?php echo assets("images/pdf.png"); ?>' width='30' height='30'></a>";
                            } else {

                                if (file.type == 'image') {
                                    html += "<img width='100%' src='" + file.url + "'> ";
                                } else {
                                    html += file.embed;
                                }
                                var element = CKEDITOR.dom.element.createFromHtml(html);
                                CKEDITOR.instances["<?php echo $id; ?>"].insertElement(element);
                            }

                        });
                    }
                },
                error: function (media_path) {
                    alert("<?php echo trans("admin::common.not_supported_file") ?>");
                }
            });


            $('.embed_poll').on('click', function (ev) {
                ev.preventDefault();
                var editor = $("iframe.cke_wysiwyg_frame").contents().find("body");
                var pollId = $(this).attr('data-id');
                console.log(pollId);
                editor.append($('<p>').html('<div name="poll" class="shortcode" id="' + pollId + '"><img src="<?php echo assets("images/polls.png"); ?>" style="max-width:50px;"></div>'));
                $('#modal_polls').modal('hide')
            });

        });
    </script>

@endpush
