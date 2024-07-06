define([
    "jquery",
    "jquery/ui"
], function($) {
    "use strict";
    $.widget('giftcard.tab', {
        _create: function() {
            var tempId = this.options.templatedId;
            console.log(this.options); 
            $('.template-name ').on('click', function(element){
                // console.log(element.target);
                var templateName = document.querySelectorAll('.template-name');
                templateName.forEach(function(template) {
                    template.style.border = '1px solid gray';
                });
                $(element.currentTarget).css('border','2px solid #058fbb')
                tempId =  element.currentTarget.getAttribute('data-templateid');
                changeDesign(tempId);
            });
            function changeDesign(templateId) {
                $('#giftcard-design-button-'+templateId).trigger('click');
                $('#template-'+templateId).css('border','2px solid #058fbb'); 
                // Hide all images
                var images = document.querySelectorAll('.label-image');
                images.forEach(function(image) {
                    image.style.display = 'none';
                });
                // Show images that belong to the selected template
                var selectedImages = document.querySelectorAll('.label-'+ templateId);
                var count =1;
                selectedImages.forEach(function(image) {
                    if(count ==1){
                        image.style.display = 'block';
                        image.childNodes[1].childNodes[1].style.border = '2px solid #058fbb';
                       // image.childNodes[1].style.border = '2px solid #058fbb';
                        count++;
                    }else{
                        image.style.display = 'block';
                    }
                });
            }
            $(document).ready(function () {
                changeDesign(tempId);
            });
            // changing design 
            $('.template-image').on('click', function(element){
                var indexImage = parseInt($(element.currentTarget).attr('data-indexid'));
                var templateId = parseInt($(element.currentTarget).attr('data-templateid'));
                var otherImages = document.querySelectorAll('.label-'+templateId);
                otherImages.forEach(function(otherimage){
                    otherimage.childNodes[1].childNodes[1].style.border = '2px solid transparent';
                });
                var selectedImages = document.querySelectorAll('.item-template');
                var count =0;
                selectedImages.forEach(function(imageTemplate) {
                    if(count == indexImage){
                        $('.item-template').eq(indexImage).trigger('click');
                        element.currentTarget.style.border='2px solid #058fbb';
                        return false;
                    }
                    count++;
                });
            }); 
        }
        
    });
    return $.giftcard.tab;
});