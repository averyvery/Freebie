<?php

$lang = array(
  'to_ignore' => '<h4>Freebie segments:</h4>
                  <p style="font-weight: normal">
                    EE will act as if these segments aren&rsquo;t in the URI at all.</p>
  
                      <div style="margin: 20px 0 0; border-top: 1px solid #ccc; font-weight: normal; font-style: italic">
                        <ul>
                          <li style="margin: 10px 0 10px">
                            success|error|preview
                          </li>
                          <li style="margin: 10px 0 10px">
                            success error preview
                          </li>
                          <li style="margin: 10px 0 10px">
                            success<br />error<br />preview
                          </li>
                          <li style="margin: 10px 0 10px">
                            inky*clyde <span style="font-style:italic">
                            (matches inkyblinkypinkyclyde)</em></span>
                          </li>
                        </ul>                        
                      </div>',
                      
  'ignore_beyond' => '<h4>Breaking segments:</h4>
                      <p style="font-weight: normal">
                        All segments AFTER one of these matches will be ignored.</p>
                      
                      <p style="font-weight: normal;">
                        Example: The URI about/<strong>map</strong>/virginia/arlington/22201,
                        will process as about/<strong>map</strong> if you set <strong>map</strong> 
                        as a breaking segment</p>',
                        
  'break_category' => '<h4>Break on category URL indicator </h4>
                         <p style="font-weight: normal">
                         Set the URL indicator 
                         <a href="'.BASE.'&C=admin_content&M=global_channel_preferences">here</a>
                       </p>',
                                               
  'remove_numbers' => '<h4>Ignore numeric segments </h4>
                         <p style="font-weight: normal">
                         Examples: /2010/, /2/, /101/</p>',

  'always_parse' => '<h4>Always Parse:</h4>
                         <p style="font-weight: normal">
                           If you have segments you NEVER want Freebie to screw with, set them here.
                           (Example: search)</p>',

  'always_parse_pagination' => '<h4>Always Parse Pagination:</h4>'
   
                      
);
