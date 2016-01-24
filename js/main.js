function toggleSpinner()
{
    $("#spinner").toggle("fast");
}

function getCountries()
{
    $(function()
    {
        toggleSpinner();
        
        $.getJSON("query.php?source=countries", function(data)
        {
            var items = [];
            $.each(data, function(index, value)
            {
                items.push("<li id=\"" + value["id"] + "\" class=\"countryItem\"><div><img src=\"" + value["img"] + "\" alt=\"\" /> <span>" + value["name"] + "</span></div></li>");
            });
            
            $("ul.secondLevelList").fadeOut(function()
            {
                $(this).remove();
            });
            
            $("<ul/>",
            {
                "class": "secondLevelList",
                html: items.join(""),
            }).appendTo("body").fadeIn();
            
            $("ul.secondLevelList li").click(function(e)
            {
                getUniversities($(this).attr("id"));
            });
        }).always(toggleSpinner);
    });
}

function getUniversities(code)
{
    toggleSpinner();
    var url = "query.php?source=universities" + (code != "" && code != undefined ? "&country=" + code:"");

    $(function()
    {
        $.getJSON(url, function(data)
        {
            var items = [];
            $.each(data, function(index, value)
            {
                items.push("<li id=\"" + value["id"] + "\" class=\"universityItem\"><div><img src=\"" + value["img"] + "\" alt=\"\" /> <span>" + value["name"] + "</span></div></li>");
            });
            
            $("ul.secondLevelList").fadeOut(function()
            {
                $(this).remove();
            });
            
            $("<ul/>",
            {
                "class": "secondLevelList",
                html: items.join(""),
            }).appendTo("body").fadeIn();
            
            $("ul.secondLevelList li").click(function(e)
            {
                getDegrees($(this).attr("id"));
            });
        }).always(toggleSpinner);
    });
}

function getDegrees(code)
{
    toggleSpinner();
    var url = "query.php?source=degrees" + (code != ""? "&university=" + code:"");
    
    $(function()
    {
        $.getJSON(url, function(data)
        {
            var items = [];
            $.each(data, function(index, value)
            {
                items.push("<li id=\"" + value["id"] + "\" class=\"degreeItem\"><div> <span>" + value["name"] + "</span></div></li>");
            });
            
            $("ul.secondLevelList").fadeOut(function()
            {
                $(this).remove();
            });
            
            $("<ul/>",
            {
                "class": "secondLevelList",
                html: items.join(""),
            }).appendTo("body").fadeIn();
            
            $("ul.secondLevelList li").click(function(e)
            {
                getSubjects(code, $(this).attr("id"));
            });
        }).always(toggleSpinner);
    });
}

function getSubjects(university, degree)
{
    toggleSpinner();
    var url = "query.php?source=subjects&university=" + university + "&degree=" + degree;
    console.log(url);
    
    $(function()
    {
        $.getJSON(url, function(data)
        {
            var items = [];
            $.each(data, function(index, value)
            {
                items.push("<li id=\"" + value["id"] + "\" class=\"subjectItem\"><div><span>" + value["name"] + "</span> <span class=\"description\"><br />Credits: " + value["credits"] + "<br />Language: " + value["language"] + "<br />Semester: " + value["semester"] + "</span></div></li>");
            });
            
            $("ul.secondLevelList").fadeOut(function()
            {
                $(this).remove();
            });
            
            $("<ul/>",
            {
                "class": "secondLevelList",
                html: items.join(""),
            }).appendTo("body").fadeIn();
            
            $("ul.secondLevelList li").click(function(e)
            {
                console.log(this);
                $(this).children("div .description").toggle();
                $(this).toggleClass("subjectItemActive");
            });
        }).always(toggleSpinner);
    });
}

function search(e)
{
    toggleSpinner();
    var keywords = $("#searchBox").val();
    
    if (keywords != "")
    {
        $.getJSON("query.php?source=search&keywords=" + keywords, function(data)
        {
            var items = [];
            $.each(data["subjects"], function(index, value)
            {
                var univname = "";
                var id_univ  = parseInt(value["id_university"]);

                for (var i = 0; i < data["universities"].length; ++i)
                    if (parseInt(data["universities"][i]["id"]) == id_univ)
                        univname = data["universities"][i]["name"];

                items.push("<li id=\"" + value["id"] + "\" class=\"subjectItem\"><div><span>" + value["name"] + "</span> <span class=\"description\"><br />Credits: " + value["credits"] + "<br />Language: " + value["language"] + "<br />Semester: " + value["semester"] + "<br />University: " + univname + "</span></div></li>");
            });

            $("ul.secondLevelList").fadeOut(function()
            {
                $(this).remove();
            });

            $("<ul/>",
            {
                "class": "secondLevelList",
                html: items.join(""),
            }).appendTo("body").fadeIn();

            $("ul.secondLevelList li").click(function(e)
            {
                console.log(this);
                $(this).children("div .description").toggle();
                $(this).toggleClass("subjectItemActive");
            });

            for (var i = 0; i < marks.length; ++i)
                marks[i].setMap(null);
            marks = [];

            setMarkers(data["universities"]);
        }).fail(function()
        {
            alert("No subjects were found.");
        }).always(toggleSpinner);
    }

    return false;
}

function mapInit()
{
    mapCanvas = document.getElementById("map");
    var mapOptions = {
        center: new google.maps.LatLng(48.261255, 8.875976),
        zoom: 5,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    map = new google.maps.Map(mapCanvas, mapOptions);
    marks = [];
    infowindows = [];

    $.getJSON('query.php?source=universities', setMarkers);
}

function setMarkers(data)
{
    var i = 0;
    $.each(data, function(key, value)
    {
        marks[i] = new google.maps.Marker({
            position: {
                lat: parseFloat(value["lat"]),
                lng: parseFloat(value["lng"])
            },
            map: map,
            title: value["name"]
        });


        marks[i].addListener('click', function()
        {
            new google.maps.InfoWindow({
                content: "<img src=\"" + value["img"] + "\" alt=\"\" class=\"minilogo\" /><span><b>" + value["name"] + "</b><br />" + value["city_name"] + "</span>"
            }).open(map, this);
        });

        ++i;
    });
}

$(function()
{
    $("#countriesButton").click(getCountries);
    $("#universitiesButton").click(function(){getUniversities();});
    $("#searchbar").submit(search);
    $("#mglass").click(function()
    {
        $("#searchBar").submit();
    });

    google.maps.event.addDomListener(window, 'load', mapInit);
});