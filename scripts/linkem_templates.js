/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function nodeTemplateList(){
    
    var that = this;
    
    this.nodeTemplates = [];
    
    this.addNewTemplate = function(){
        
        $.post(ajaxUrl, {
            action: "node_template_load"
        }).success(function (data) {
            
        }).fail(function (err) {
            console.log(err);
        });
    };
    
    this.deleteTemplate = function(){

    };
    

    
    this.loadTemplates = function (){
        $.post(ajaxUrl, {
            action: "node_template_load"
        }).success(function (data) {
            
        }).fail(function (err) {
            console.log(err);
        });
    };
    
}


function nodeTemplate (){
    
    var that = this;
    
    this.properties=[];
    
    this.delete = function(){
        $.post(ajaxUrl, {
            action: "node_template_delete"
        }).success(function (data) {
            
        }).fail(function (err) {
            console.log(err);
        });
    };
    
    
    
}

function propertyDefinition(){
    
    var that = this;
    
    this.name = "";
    
    this.type ="";
    
    this.default ="";
    
    
    
}