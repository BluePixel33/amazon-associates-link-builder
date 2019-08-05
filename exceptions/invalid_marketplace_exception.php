<?php

namespace AmazonAssociatesLinkBuilder\exceptions;
/*
Copyright 2016-2018 Amazon.com, Inc. or its affiliates. All Rights Reserved.

Licensed under the GNU General Public License as published by the Free Software Foundation,
Version 2.0 (the "License"). You may not use this file except in compliance with the License.
A copy of the License is located in the "license" file accompanying this file.

This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
either express or implied. See the License for the specific language governing permissions
and limitations under the License.
*/

/**
 *
 *  Exception for Invalid Marketplace passed
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/exceptions
 */
class Invalid_Marketplace_Exception extends \Exception {
    public function errorMessage() {
        return 'Error on line ' . $this->getLine() . ' in ' . $this->getFile() . ':' . $this->getMessage();
    }
}

?>