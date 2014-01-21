/**
 * Interaction for the Events module
 *
 * @author Wouter Verstuyf <info@webflow.be>
 */
jsBackend.events =
{
	// constructor
	init: function()
	{
		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	}
}

$(jsBackend.events.init);
