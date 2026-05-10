<?php

test('the root route redirects to api documentation', function () {
    $this->get('/')->assertRedirect('/api/documentation');
});
