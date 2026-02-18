/**
 * Grade Validation and Transformation Script
 * Automatically converts grades from 10-70 range to 1.0-7.0 range
 * Usage: Include this script in any form with a #nota-input field
 */

$(document).ready(function() {
    // Grade validation and transformation
    $('#nota-input').on('blur', function() {
        var value = $(this).val().trim();
        
        // If empty, skip
        if (!value) return;
        
        // Remove any non-numeric characters except decimal point
        value = value.replace(/[^\d.]/g, '');
        
        // Convert to number
        var numValue = parseFloat(value);
        
        // Check if it's in the range 10-70 (needs conversion)
        if (numValue >= 10 && numValue <= 70) {
            // Convert to decimal format (e.g., 50 -> 5.0, 35 -> 3.5)
            numValue = numValue / 10;
            $(this).val(numValue.toFixed(1));
            
            // Show feedback
            var feedbackDiv = $(this).parent().find('.grade-feedback');
            if (feedbackDiv.length === 0) {
                feedbackDiv = $('<div class="grade-feedback text-success small mt-1"></div>');
                $(this).parent().append(feedbackDiv);
            }
            feedbackDiv.text('✓ Nota convertida automáticamente a ' + numValue.toFixed(1));
            
            setTimeout(function() {
                feedbackDiv.fadeOut(function() { $(this).remove(); });
            }, 3000);
        } else if (numValue >= 1 && numValue <= 7) {
            // Already in correct format, just format it
            $(this).val(numValue.toFixed(1));
        } else {
            // Invalid range
            $(this).val('');
            var feedbackDiv = $(this).parent().find('.grade-feedback');
            if (feedbackDiv.length === 0) {
                feedbackDiv = $('<div class="grade-feedback text-danger small mt-1"></div>');
                $(this).parent().append(feedbackDiv);
            }
            feedbackDiv.text('✗ La nota debe estar entre 1.0 y 7.0');
            
            setTimeout(function() {
                feedbackDiv.fadeOut(function() { $(this).remove(); });
            }, 3000);
        }
    });
});
