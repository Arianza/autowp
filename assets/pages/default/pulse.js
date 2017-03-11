var $ = require('jquery');
var Raphael = require('raphael');

module.exports = {
    init: function() {
        
        Raphael.fn.drawGrid = function (x, y, w, h, wv, hv, color) {
            color = color || "#000";
            var path = ["M", Math.round(x) + 0.5, Math.round(y) + 0.5, "L", Math.round(x + w) + 0.5, Math.round(y) + 0.5, Math.round(x + w) + 0.5, Math.round(y + h) + 0.5, Math.round(x) + 0.5, Math.round(y + h) + 0.5, Math.round(x) + 0.5, Math.round(y) + 0.5],
                rowHeight = h / hv,
                columnWidth = w / wv;
            for (var i = 1; i < hv; i++) {
                path = path.concat(["M", Math.round(x) + 0.5, Math.round(y + i * rowHeight) + 0.5, "H", Math.round(x + w) + 0.5]);
            }
            for (i = 1; i < wv; i++) {
                path = path.concat(["M", Math.round(x + i * columnWidth) + 0.5, Math.round(y) + 0.5, "V", Math.round(y + h) + 0.5]);
            }
            return this.path(path.join(",")).attr({stroke: color});
        };

        

        $('#pulse-graph').each(function() {
            
            var $element = $(this);
            
            var values = $element.data('values');
            
            // Grab the data
            var //labels = [],
                maxes = [],
                lines = [];
            
            $.each(values, function(userId, info) {
                //labels = [];
                var line = [];
                $.each(info.line, function(date, value) {
                    //labels.push(date);
                    line.push(value);
                });
                
                lines.push({
                    userId: userId,
                    line: line,
                    color: info.color
                });
                
                maxes.push(Math.max.apply(Math, line));
            });
            
            var max = Math.max.apply(Math, maxes);
            
            // Draw
            var width = $element.width(),
                height = $element.height(),
                leftgutter = 30,
                bottomgutter = 50,
                topgutter = 20,
                r = Raphael(this, width, height),
                labelsCount = lines[0].line.length,
                X = (width - leftgutter) / labelsCount,
                Y = (height - bottomgutter - topgutter) / max;
            
            r.drawGrid(
                leftgutter + X * 0.5 + 0.5, 
                topgutter + 0.5, 
                width - leftgutter - X, 
                height - topgutter - bottomgutter, 
                labelsCount-1, 
                10, 
                "#000"
            );
            
            var columnWidth = (width - leftgutter - X) / (labelsCount-1);
            
            var map = {};
            
            $.map(lines, function(line) {
                
                var rects = [];
                
                var data = line.line;
                
                var color = line.color;
                
                var cWidth = columnWidth;
                for (var i = 0, ii = labelsCount; i < ii; i++) {
                    var value = data[i],
                        cHeight = Y * value,
                        y = Math.round(height - bottomgutter - cHeight),
                        x = Math.round(leftgutter + X * (i + 0.5));
                    
                    if (value) {
                        rects.push(r.rect(x - cWidth/2, y, cWidth, Math.round(cHeight)).attr({
                            fill: color,
                            opacity: 0.9,
                            stroke: color
                        }));
                    }
                }
                
                map[line.userId] = rects;
            });
            
            $('.legend > span').hover(function() {
                $.map(map, function(rects) {
                    $.map(rects, function(rect) {
                        rect.attr({
                            opacity: 0.1
                        });
                    });
                });
                
                var uid = $(this).data('id');
                $.map(map[uid], function(rect) {
                    rect.attr({
                        opacity: 1
                    });
                });
            }, function() {
                $.map(map, function(rects) {
                    $.map(rects, function(rect) {
                        rect.attr({
                            opacity: 0.9
                        });
                    });
                });
            });
        });
        
    }
};
