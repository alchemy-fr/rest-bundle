<?php

namespace Alchemy\Rest\Request;

interface SortOptions
{

    const SORT_ASC = 'asc';

    const SORT_DESC = 'desc';

    /**
     * @param array $sortMap
     * @return Sort[]
     */
    public function getSorts(array $sortMap = array());
}
