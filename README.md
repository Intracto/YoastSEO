- Install Yoast SEO js plugin via Libraries
- Create custom field that will hold focus keyword and status
- How to decide on which html the tool should run
    - Rendered node
    - Configured fields
    
    
    
Put an event listener to every input element
In the callback function, trigger a hidden element which has an #ajax callback configured to it
Use the AjaxResponse of that callback to determine the new score