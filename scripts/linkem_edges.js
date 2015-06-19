/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/*
 * Edge Class
 */
function Edge(source, target, type, isNew) {
    var that = this;

    this.properties = {};

    this.createId = function () {
        that.id = guid();
    };

    this.retrieveId = function () {
        that.id = that.properties.uniqueId;
    };

    this.updateProperty = function (propName, propValue) {
        $.post(ajaxUrl, {
            action: "link_update",
            uniqueId: that.id,
            propName: propName,
            propValue: propValue
        }).success(function (data) {
            // when success delete the node from the graph
            that.properties[propName] = propValue;
        }).fail(function (err) {
            console.log(err);
        });

    };

    if (isNew) {
        this.createId();
    }
    this.type = type;
    this.source = source;
    this.target = target;
}
