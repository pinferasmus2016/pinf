function toggleSpinner()
{
    $("#spinner").toggle("fast");
}

function getCountries()
{
    $(function()
    {
        toggleSpinner();
        
        $.getJSON("../query.php?source=countries", function(data)
        {
            var items = [];
            $.each(data, function(index, value)
            {
                items.push("<option value=\"" + value["id"] + "\" >" + value["name"] + "</option>");
            });

            $("select[name='country']").html(items.join(""));
        }).always(toggleSpinner);
    });
}

function getUniversities(code)
{
    toggleSpinner();
    var url = "../query.php?source=universities" + (code != "" && code != undefined ? "&country=" + code:"");

    $(function()
    {
        $.getJSON(url, function(data)
        {
            var items = [];
            items.push("<option value=\"\" selected></option>");
            $.each(data, function(index, value)
            {
                items.push("<option value=\"" + value["id"] + "\" >" + value["name"] + "</option>");
            });

            $("select[name='university']").html(items.join(""));
        }).always(toggleSpinner);
    });
}

function getDegrees(code)
{
    toggleSpinner();
    var url = "../query.php?source=degrees" + (code != ""? "&university=" + code:"");
    
    $(function()
    {
        $.getJSON(url, function(data)
        {
            var items = [];
            $.each(data, function(index, value)
            {
                items.push("<option value=\"" + value["id"] + "\" >" + value["name"] + "</option>");
            });

            $("select[name='degree']").html(items.join(""));
        }).always(toggleSpinner);
    });
}