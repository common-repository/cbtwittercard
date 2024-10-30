(function ( $ ) {
	"use strict";

    //document ready
    $( document ).ready(function() {
        //adding tooltip
        $('.cbtwittercard_tooltip').tooltipster();

        //multi selection
        var selected_array =[];
        $( "select.multiselect" )
            .change(function () {
                var root = $(this).parents('.text');
                $(root).find(".multi-select").each(function(){
                    $(this).removeAttr('checked','checked');

                });

                $(root).find( "select option:selected" ).each(function() {
                    var str = $( this ).val();

                    selected_array.push(str);
                    $(root).find(".multi-select").each(function(){
                        if($(this).val() ==str ){
                            // console.log($(this).val());
                            $(this).attr('checked','checked');
                        }
                    });
                });
                // $( "div.text" ).text( str );
            })
            .change();
        //initialize chosen plugin option
        var config = {
            '.chosen-select'           : {},
            '.chosen-select-deselect'  : {allow_single_deselect:true},
            '.chosen-select-no-single' : {disable_search_threshold:10},
            '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
            '.chosen-select-width'     : {width:"95%"}
        }
        for (var selector in config) {
            $(selector).chosen(config[selector]);
        }
        //multi select ends


       //setting panel
        $('.cbtwittercard-group').hide();
        var activetab = '';
        if (typeof(localStorage) != 'undefined' ) {
            activetab = localStorage.getItem("cbtwittercard-activetab");
        }
        if (activetab != '' && $(activetab).length ) {
            $(activetab).fadeIn();
        } else {
            $('.cbtwittercard-group:first').fadeIn();
        }
        $('.cbtwittercard-group .collapsed').each(function(){
            $(this).find('input:checked').parent().parent().parent().nextAll().each(
                function(){
                    if ($(this).hasClass('last')) {
                        $(this).removeClass('hidden');
                        return false;
                    }
                    $(this).filter('.hidden').removeClass('hidden');
                });
        });

        if (activetab != '' && $(activetab + '-tab').length ) {
            $(activetab + '-tab').addClass('cbtwittercard-nav-tab-active');
        }
        else {
            $('.nav-tab-wrapper a:first').addClass('cbtwittercard-nav-tab-active');
        }
        $('.nav-tab-wrapper a').click(function(evt) {
            $('.nav-tab-wrapper a').removeClass('cbtwittercard-nav-tab-active');
            $(this).addClass('cbtwittercard-nav-tab-active').blur();
            var clicked_group = $(this).attr('href');
            if (typeof(localStorage) != 'undefined' ) {
                localStorage.setItem("cbtwittercard-activetab", $(this).attr('href'));
            }
            $('.cbtwittercard-group').hide();
            $(clicked_group).fadeIn();
            evt.preventDefault();
        });
        //setting panel ends


        // Runs when the image button is clicked.
        $(".cbtwittercard-wpsa-browse").live('click', function(e) {
           // e.preventDefault();

            var _this = this;
            _this.textid =  jQuery(_this).attr('data-id');

            if ( _this.meta_image_frame ) {

                _this.meta_image_frame.open();
                return;
            }
            else{

                // Sets up the media library frame
                _this.meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
                    title: 'Upload Image',
                    button: {
                        text: 'Select'
                    },
                    library: {
                        type: 'image'
                    }
                });
                _this.meta_image_frame.open();
            }

            _this.meta_image_frame.on('select', function(){
                
                console.log(_this.textid);

                // Grabs the attachment selection and creates a JSON representation of the model.
                _this.media_attachment = _this.meta_image_frame.state().get('selection').first().toJSON();

                $('#'+_this.textid).val(_this.media_attachment.url);
                $('#'+_this.textid+'_review').html('<img style="max-width:200px;" src="'+_this.media_attachment.url+'"/>');
                if(_this.textid.match('photo_settings')){
                    $('#'+_this.textid+'width').val(_this.media_attachment.width);
                    $('#'+_this.textid+'height').val(_this.media_attachment.height);
                }
            });
        });


        // repeat remove button
        // js for callback_repeat remove button
        $('.cbtwittercard-repeatfields-wrapper').on('click' ,'.cbtwittercard-remove',function(e){

            var lastcount = $('.cbtwittercard-add-new').attr('data-count');

            if(lastcount > 1){
                var newcount  = --lastcount;
                $('.cbtwittercard-add-new').attr('data-count' , newcount);
                $('.cbtwittercard-repeatfields-count').val(newcount);
                $(this).parents('li').remove();
                e.preventDefault();
            }
        });


        //<li><input type="text" value=" " name="_cbtwittercard_meta_product_settings[_cbtwittercard_meta_product][_cbtwittercard_meta_productlabel][]" id="_cbtwittercard_meta_product_settings_cbtwittercard_meta_product_cbtwittercard_meta_productlabel[0]" class="regular-text _cbtwittercard_meta_productlabel" style="width:100px;height: 30px;margin-bottom: 5px;"><input type="text" value=" " name="_cbtwittercard_meta_product_settings[_cbtwittercard_meta_product][_cbtwittercard_meta_productdata][]" id="_cbtwittercard_meta_product_settings_cbtwittercard_meta_product_cbtwittercard_meta_productdata[0]" class="regular-text _cbtwittercard_meta_productdata" style="width:100px;height: 30px;margin-bottom: 5px;"><a class="button cbtwittercard-remove" href="#">Remove</a></li>

        // js for callback_repeat add button
        $('.cbtwittercard-add-new').on('click',function(e){
            e.preventDefault();

            var $html = '<li>';
            var section  = $(this).attr('data-section');
            var groupid       = $(this).attr('data-id');
            var count    = $(this).attr('data-count');
            var labels   = $('.cbtwittercard-repeatfields-groups');
            $.each( labels, function( key, entry ) {
                var label = $(labels[key]).val();
                $html += '<input type="text" style="width:100px;height: 30px;margin-bottom: 5px;" class="regular-text '+label+'" id="'+section+groupid+label+'['+count+']" name="'+section+'['+groupid+']['+label+'][]" value=""/>';
            });

            $html += '<a href="#" class="button cbtwittercard-remove">'+cbtwittercardadmin.remove+'</a>';
            $html += '</li>';



            $('.cbtwittercard-repeatfields').append($html);
            var newcount = ++count;
            $('.cbtwittercard-add-new').attr('data-count' , newcount);
            $('.cbtwittercard-repeatfields-count').val(newcount);

        });

        // meta box settings
        $('.cbtwittercard-meta-group').hide();
        var activetab = '';
        if (typeof(localStorage) != 'undefined' ) {
            activetab = localStorage.getItem("cbtwittercard-meta-activetab");
        }
        if (activetab != '' && $(activetab).length ) {
            $(activetab).fadeIn();
        } else {
            $('.cbtwittercard-meta-group:first').fadeIn();
        }
        $('.cbtwittercard-meta-group .collapsed').each(function(){
            $(this).find('input:checked').parent().parent().parent().nextAll().each(
                function(){
                    if ($(this).hasClass('last')) {
                        $(this).removeClass('hidden');
                        return false;
                    }
                    $(this).filter('.hidden').removeClass('hidden');
                });
        });

        if (activetab != '' && $(activetab + '-tab').length ) {
            $(activetab + '-tab').addClass('cbtwittercard-meta-nav-tab-active');
        }
        else {
            $('.cbtwittercard-meta-nav-tab-wrapper a:first').addClass('cbtwittercard-meta-nav-tab-active');
        }
        $('.cbtwittercard-meta-nav-tab-wrapper a').click(function(evt) {
            evt.preventDefault();

            $('.cbtwittercard-meta-nav-tab-wrapper a').removeClass('cbtwittercard-meta-nav-tab-active');
            $(this).addClass('cbtwittercard-meta-nav-tab-active').blur();
            var clicked_group = $(this).attr('href');
            if (typeof(localStorage) != 'undefined' ) {
                localStorage.setItem("cbtwittercard-meta-activetab", $(this).attr('href'));
            }
            $('.cbtwittercard-meta-group').hide();
            $(clicked_group).fadeIn();

        });
        // meta box

    });


}(jQuery));