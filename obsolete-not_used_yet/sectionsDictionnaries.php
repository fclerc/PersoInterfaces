<?php
$h1s = array(
			'profile' => 'Profile selection',
			'liveContext' => 'Live Context Selection',
			'sequenceContext' => 'Sequence Context Selection',
			'strategy' => 'Strategy Selection'
);

$instructions = array(
			'profile' => 'In this section you can select a profile, to edit its values and realize tests with you strategies.',
			'liveContext' => 'In this section you can select a live context, to edit its values and realize tests with you strategies.',
			'sequenceContext' => 'Select the sequence context for a week, and you will be able to edit its content.',
			'strategy' => 'Select a strategy and edit it to define what you think is best for the learners.'
);

$paths = array(
			'profile' => 'resources/teacher/profiles',
			'liveContext' => 'resources/teacher/liveContexts',
			'sequenceContext' => 'resources/teacher/sequenceContexts',
			'strategy' => 'resources/teacher/strategies'
);

$interfaces = array(
			'profile' => 'fileValuesModification.html',
			'liveContext' => 'fileValuesModification.html',
			'sequenceContext' => 'fileValuesModification.html',
			'strategy' => 'rules_definition.html'
);
$res = array('h1' => $h1s,'instruction' => $instructions,'path' => $paths,'interface' => $interfaces);
echo json_encode($res);
?>