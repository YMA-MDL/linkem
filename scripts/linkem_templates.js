
/*****************************
 *  nodeTemplateList Class   *
 ****************************/

function nodeTemplateList(DOM) {

    // properties

    var that = this;

    this.DOM = DOM;

    this.selected = {
        type: '',
        id: '',
        selected: false
    };

    this.nodeTemplates = [];

    this.edgeTemplates = [];

    this.addNodeButton = this.DOM.find("#addNodeTemplate");

    this.addEdgeButton = this.DOM.find("#addEdgeTemplate");

    this.nodeTemplateList = this.DOM.find("#nodeTemplateSelection");

    this.edgeTemplateList = this.DOM.find("#edgeTemplateSelection");

    this.nodeTemlateLabelList = this.DOM.find("#nodeLabelSelection");

    this.edgeSourceList = this.DOM.find("#edgeTemplateSource");

    this.edgeTargetList = this.DOM.find("#edgeTemplateTarget");

    this.nodeTemplateUsageList = $(".nodeTemplateList");

    this.nodeTemplateUsageListWithEmpty = $(".nodeTemplatePropListWithEmpty");

    this.pictureDomSelector = this.DOM.find("#nodeTemplateImage");

    this.noImagePicture = "./images/nodeTypes/noimage128.png";

    this.templatePropertiesDOM = that.DOM.find(".TemplateProperties");

    this.propertyTypes = [
        "string", "date", "dateTime", "integer"
    ];
    this.propertyTypeList = "";
    for (var i = 0; i < that.propertyTypes.length; i++) {
        that.propertyTypeList += "<option value='" + that.propertyTypes[i] + "'>" + that.propertyTypes[i] + "</option>";
    }

    // functions

    this.addNewNodeTemplate = function (templateName) {
        $.post(ajaxUrl, {
            action: "node_template_add",
            name: templateName,
            share: true
        }).success(function (data) {
            var nodeTemplateJson = {
                "properties": {
                    "_name": templateName,
                    "_label": "_name",
                    "_id": data.id
                }
            };
            that.nodeTemplates.push(new nodeTemplate(nodeTemplateJson));
            that.loadTemplates();
            standardNotification("new node template added : " + templateName, "success");
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.getTemplateByName = function(templateName){
            for (var i = 0; i < that.nodeTemplates.length; i++) {
                if (that.nodeTemplates[i].name === templateName){
                    return that.nodeTemplates[i];
                }
            }
    };
    this.addNewEdgeTemplate = function (templateName) {
        $.post(ajaxUrl, {
            action: "edge_template_add",
            name: templateName
        }).success(function (data) {
            var edgeTemplateJson = {
                "properties": {
                    "_name": templateName,
                    "_label": "_name",
                    "_sourceType": [],
                    "_targetType": [],
                    "_id": data.id
                }
            };
            that.edgeTemplates.push(new edgeTemplate(edgeTemplateJson));
            that.loadTemplates();
            standardNotification("new edge tTemplate added : " + templateName, "success");
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.deleteTemplate = function () {

    };

    this.loadNodeTemplateList = function () {
        var options = "";
        for (var i = 0; i < that.nodeTemplates.length; i++) {
            options += "<option value='" + i + "' name='" + that.nodeTemplates[i].name + "'>" + that.nodeTemplates[i].name + "</option>";
        }
        that.nodeTemplateList.html(options);
        that.nodeTemplateList.prepend('<option selected value="emptyOption"></option>');
        that.nodeTemplateUsageListWithEmpty.html(options);
        that.nodeTemplateUsageList.html(options);
    };

    this.loadEdgeTemplateList = function () {
        var options = "";
        for (var i = 0; i < that.edgeTemplates.length; i++) {
            options += "<option value='" + i + "'>" + that.edgeTemplates[i].name + "</option>";
        }
        that.edgeTemplateList.html(options);
        that.edgeTemplateList.prepend('<option selected value="emptyOption"></option>');
    };


    this.loadTemplates = function () {
        $.post(ajaxUrl, {
            action: "templates_load"
        }).success(function (data) {
            that.nodeTemplates = [];
            for (var i = 0; i < data.content_nodes.length; i++) {
                that.nodeTemplates.push(new nodeTemplate(data.content_nodes[i]));
            }
            that.edgeTemplates = [];
            for (var i = 0; i < data.content_edges.length; i++) {
                that.edgeTemplates.push(new edgeTemplate(data.content_edges[i]));
            }
            that.loadNodeTemplateList();
            that.loadEdgeTemplateList();

            //update graphs
            updateGraphStyleSheetImages();

        }).fail(function (err) {
            console.log(err);
        });
    };

    this.addPropertyEntry = function (propName, propType) {
        that.templatePropertiesDOM.find("table").append(
                "<tr propertyName='" + propName + "'><td >" + propName + "</td>"
                + "<td ><select class='col-md-5 form-control templatePropertyTypeChange' id='typeFor" + propName + "' >" + that.propertyTypeList + "</select></td >"
                + "<td ><div class='btn-group ' role='group' >"
                + "<button type='button' class=' btn btn-warning editTemplatePropertyName'><span class='glyphicon glyphicon-edit'></span></button>"
                + "<button type='button' class=' btn btn-danger deleteTemplateProperty'><span class='glyphicon glyphicon-trash'></span></button>"
                + "</div></td > </tr>"
                );
        that.templatePropertiesDOM.find("select#typeFor" + propName).val(propType);
        that.templatePropertiesDOM.find("select#typeFor" + propName).change(function () {
            if (that.selected.type === 'node') {
                that.nodeTemplates[that.selected.id].updatePropType(propName, $(this).val());
            } else {
                that.edgeTemplates[that.selected.id].updatePropType(propName, $(this).val());
            }
        });
    };



    this.removePropFromTemplate = function (selection, propName) {
        $.post(ajaxUrl, {
            action: "node_template_deleteProperty",
            propertyName: propName,
            nodeId: that.nodeTemplates[selection].properties._id
        }).success(function (data) {
            delete that.nodeTemplates[selection].properties[propName];
            that.DOM.find(".TemplateProperties").find("tr[propertyName='" + propName + "']").remove();
        }).fail(function (err) {
            console.log(err);
        });
    };

    // events

    this.addNodeButton.click(function () {
        var nodeTemplateName = prompt("Please enter the node template name", "");
        if (nodeTemplateName != null) {
            nodeTemplateName=nodeTemplateName.convert_case();
            that.addNewNodeTemplate(removeDiacritics(nodeTemplateName));
        }
    });

    this.addEdgeButton.click(function () {
        var edgeTemplateName = prompt("Please enter the edge template name", "");
        if (edgeTemplateName != null) {
            edgeTemplateName=edgeTemplateName.convert_case();
            that.addNewEdgeTemplate(removeDiacritics(edgeTemplateName));
        }
    });

    this.edgeSourceList.change(function () {
        var selection = [];

        $(this).find(":selected").each(function () {
            selection.push($(this).text());
        });
        that.edgeTemplates[that.selected.id].updateSource(selection);
    });

    this.edgeTargetList.change(function () {
        var selection = [];

        $(this).find(":selected").each(function () {
            selection.push($(this).text());
        });
        that.edgeTemplates[that.selected.id].updateTarget(selection);
    });

    this.nodeTemlateLabelList.change(function(){
        var selection=[];
        $(this).find(":selected").each(function () {
            selection.push($(this).text());
        });
        that.nodeTemplates[that.selected.id].updateLabel("_label",selection.join());
    });

    this.nodeTemplateList.change(function () {
        var selection = $(this).val();
        $("#templateNodeSettings").removeClass("hidden");
        $("#edgeTemplateSelection").val("");
        $("#templateEdgeSettings").addClass("hidden");
        if (selection === "emptyOption") {
            // empty name
            that.DOM.find("#nodeTemplateName").val("");
            // empty properties
            that.templatePropertiesDOM.html("<table class='table table-condensed'></table>");
            // empty image
            that.pictureDomSelector.attr("src", that.noImagePicture);
        } else {
            // set as seleced
            that.selected = {
                type: "node",
                id: selection,
                selected: true
            };
            // fill name
            that.DOM.find("#nodeTemplateName").val(that.nodeTemplates[selection].name);

            // fill label
            var labelSelectionList = '';
            for (var key in that.nodeTemplates[selection].properties) {
                if (!(key.startsWith("_"))){
                    labelSelectionList += "<option value='" + key + "'>" + key + "</option>";
                }
            }
            that.DOM.find("#nodeLabelSelection").html(labelSelectionList);
            // set label selected
            console.log(that.nodeTemplates[selection]);
            $.each(that.nodeTemplates[selection].label.split(","), function (i, e) {
                console.log(e);
                 that.DOM.find("#nodeLabelSelection option[value='"+e+"']").prop("selected", true);
            });
            // fill properties
            /// build property type list

            /// load existing properties
            that.templatePropertiesDOM.html("<table class='table table-condensed'></table>");
            var object = that.nodeTemplates[selection].properties;
            for (var key in object) {
                if (object.hasOwnProperty(key)) {
                    if (!(key.startsWith('_'))) {
                        that.addPropertyEntry(key, object[key]);
                    }
                }
            }
            that.templatePropertiesDOM.append("<button class='btn btn-success btn-block col-md-12 addTemplateProperty' >add property</button>");
            that.DOM.find(".addTemplateProperty").click(function () {
                var nodeTemplatePropName = prompt("Please enter a property name", "");
                if (nodeTemplatePropName !== null) {
                    nodeTemplatePropName=nodeTemplatePropName.convert_case();
                    that.nodeTemplates[that.selected.id].addNewProperty(nodeTemplatePropName, that);
                    that.addPropertyEntry(nodeTemplatePropName, 'string');
                }
            });

            that.DOM.find(".deleteTemplateProperty").click(function () {
                var propertyName = $(this).closest("tr").attr("propertyName");
                that.removePropFromTemplate(selection, propertyName);
            });
            // fill images
            that.pictureDomSelector.attr("src", that.nodeTemplates[selection].image);
        }
    });



    this.edgeTemplateList.change(function () {
        var selection = $(this).val();
        $("#templateNodeSettings").addClass("hidden");
        $("#nodeTemplateSelection").val("");
        $("#templateEdgeSettings").removeClass("hidden");
        if (selection === "emptyOption") {
            // empty name
            that.DOM.find("#TemplateName").val("");
            // empty properties
            that.templatePropertiesDOM.html("<table class='table table-condensed'></table>");
        } else {
            // fill name
            that.DOM.find("#edgeTemplateName").val(that.edgeTemplates[selection].name);
            // fill label
            var labelSelectionList = '';
            for (var key in that.edgeTemplates[selection].properties) {
                labelSelectionList += "<option value='" + key + "'>" + key + "</option>";
            }

            that.DOM.find("#edgeLabelSelection").html(labelSelectionList);
            that.DOM.find("#edgeLabelSelection").val(that.edgeTemplates[selection].label);
            $.each(that.edgeTemplates[selection].source.split(","), function (i, e) {
                 that.DOM.find("#edgeTemplateSource option[name='"+e+"']").prop("selected", true);
            });
            $.each(that.edgeTemplates[selection].target.split(","), function (i, e) {
                 that.DOM.find("#edgeTemplateTarget option[name='"+e+"']").prop("selected", true);
            });
            // fill properties
            /// build property type list

            /// load existing properties
            that.templatePropertiesDOM.html("<table class='table table-condensed'></table>");
            var object = that.edgeTemplates[selection].properties;
            for (var key in object) {
                if (object.hasOwnProperty(key)) {
                    if (!(key.startsWith('_'))) {
                        that.addPropertyEntry(key, object[key]);
                    }
                }
            }
            that.templatePropertiesDOM.append("<button class='btn btn-success btn-block col-md-12 addTemplateProperty' >add property</button>");
            that.DOM.find(".addTemplateProperty").click(function () {
                var edgeTemplatePropName = prompt("Please enter a property name", "");
                if (edgeTemplatePropName != null) {
                    edgeTemplatePropName=edgeTemplatePropName.convert_case();
                    that.edgeTemplates[that.selected.id].addNewProperty(edgeTemplatePropName, that);
                    that.addPropertyEntry(edgeTemplatePropName, 'string');
                }
            });

            // set as seleced
            that.selected = {
                type: "edge",
                id: selection,
                selected: true
            };

        }
    });


    this.pictureDomSelector.click(function () {
        filepicker.setKey("AgDnQiPWvQilRLfbn2w1Mz");
        filepicker.pickAndStore({mimetype: "image/*"}, {},
                function (InkBlobs) {
                    console.log(InkBlobs[0].url);
                    that.nodeTemplates[that.selected.id].updatePicture(InkBlobs[0].url);
                    that.nodeTemplates[that.selected.id].image = InkBlobs[0].url;
                    updateGraphStyleSheetImages();
                    for (var i = 0; i < graphsSetMgr.openGraphList.length; i++) {
                        console.log("update stylesheet");
                        graphsSetMgr.openGraphList[i].cy.style(graphStylesheet);
                    }
                    that.pictureDomSelector.attr("src", InkBlobs[0].url);
                });
    });

    // constructor actions
    this.loadTemplates();

}

/*************************
 *  nodeTemplate Class   *
 ************************/

function nodeTemplate(json) {

    // properties

    var that = this;

    this.noImagePicture = "./images/nodeTypes/noimage128.png";

    this.properties = json;

    if (json._image !== undefined) {
        this.image = json._image;
    } else {
        this.image = this.noImagePicture;
    }


    this.name = json._name;
    this.label = json._label;

    // functions

    this.delete = function () {
        $.post(ajaxUrl, {
            action: "node_template_delete"
        }).success(function (data) {

        }).fail(function (err) {
            console.log(err);
        });
    };

    this.updatePropType = function (propName, propType) {
        $.post(ajaxUrl, {
            action: "node_template_updateProperty",
            propertyName: propName,
            propertyValue: propType,
            nodeId: that.properties._id
        }).success(function (data) {
            that.properties[propName] = propType;
        }).fail(function (err) {
            console.log(err);
        });
    };
    this.updateLabel = function (propName, propType) {
        $.post(ajaxUrl, {
            action: "node_template_updateProperty",
            propertyName: propName,
            propertyValue: propType,
            nodeId: that.properties._id
        }).success(function (data) {
            that.label= propType;
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.addNewProperty = function (propertyName, nodeTemplates) {
        $.post(ajaxUrl, {
            action: "node_template_addProperty",
            propertyName: propertyName,
            nodeId: that.properties._id
        }).success(function (data) {
            nodeTemplates.loadTemplates();
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.updatePicture = function (imageURL) {
        $.post(ajaxUrl, {
            action: "node_template_updateImage",
            imageUrl: imageURL,
            nodeId: that.properties._id
        }).success(function (data) {
            console.log("Yay! image updated");
        }).fail(function (err) {
            console.log(err);
        });
    };
}

/*************************
 *  edgeTemplate Class   *
 ************************/

function edgeTemplate(json) {


    // properties

    var that = this;

    this.properties = json;

    this.name = json._name;

    this.label = json._label;

    this.source = json._source;

    this.target = json._target;

    // functions

    this.delete = function () {
        $.post(ajaxUrl, {
            action: "edge_template_delete"
        }).success(function (data) {

        }).fail(function (err) {
            console.log(err);
        });
    };

    this.addNewProperty = function (propertyName, templateList) {
        $.post(ajaxUrl, {
            action: "edge_template_addProperty",
            propertyName: propertyName,
            nodeId: that.properties._id
        }).success(function (data) {
            templateList.loadTemplates();
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.updatePropType = function (propName, propType) {
        $.post(ajaxUrl, {
            action: "edge_template_updateProperty",
            propertyName: propName,
            propertyValue: propType,
            nodeId: that.properties._id
        }).success(function (data) {
            that.properties[propName] = propType;
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.updateTarget = function (nodeTypeName) {
        $.post(ajaxUrl, {
            action: "edge_template_updateProperty",
            propertyName: "_target",
            propertyValue: nodeTypeName.join(),
            nodeId: that.properties._id
        }).success(function (data) {
            that.target = nodeTypeName.join();
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.updateSource = function (nodeTypeName) {
        $.post(ajaxUrl, {
            action: "edge_template_updateProperty",
            propertyName: "_source",
            propertyValue: nodeTypeName.join(),
            nodeId: that.properties._id
        }).success(function (data) {
            that.source = nodeTypeName.join();
        }).fail(function (err) {
            console.log(err);
        });
    };
}

/*******************************
 *  propertyDefinition Class   *
 ******************************/

function propertyDefinition() {

    var that = this;

    this.name = "";

    this.type = "";

    this.default = "";

    this.isLabel = false;
}
