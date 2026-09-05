-- arahOS OIT Knowledge Base seed: categories + FAQs/tutorials
SET @now = NOW();

-- Categories (ispublic=1 so they show on the public portal)
INSERT INTO ost_faq_category (category_pid, ispublic, name, description, notes, created, updated) VALUES
(NULL, 1, 'Getting Started', 'Common questions about arahOS accounts, passwords, and accessing the help desk.', '', @now, @now),
(NULL, 1, 'Tickets & Support', 'How to open, track, and manage support tickets.', '', @now, @now),
(NULL, 1, 'Tutorials & How-To Guides', 'Step-by-step guides for common arahOS IT tasks.', '', @now, @now);

SET @cat_start = (SELECT category_id FROM ost_faq_category WHERE name='Getting Started' LIMIT 1);
SET @cat_tickets = (SELECT category_id FROM ost_faq_category WHERE name='Tickets & Support' LIMIT 1);
SET @cat_tutorials = (SELECT category_id FROM ost_faq_category WHERE name='Tutorials & How-To Guides' LIMIT 1);

-- FAQs (ispublished=1 = public)
INSERT INTO ost_faq (category_id, ispublished, question, answer, keywords, notes, created, updated) VALUES
(@cat_start, 1, 'What is a arahOS account and how do I get one?',
 '<p>A arahOS account is your official Virgin Islands Department of Education network login (usually <b>firstname.lastname</b>). It gives you access to email, the help desk, and district systems.</p><p>If you do not have an account yet, ask your school administrator or open a ticket selecting <b>Account Request</b> as the help topic.</p>',
 'account,login,arahOS,network', '', @now, @now),
(@cat_start, 1, 'How do I reset my password?',
 '<p>Visit the Password Reset Portal or contact the help desk. To reset:</p><ol><li>Go to <b>http://it.arahOS.example</b></li><li>Click <b>Forgot Password</b></li><li>Enter your arahOS email and follow the emailed instructions</li></ol><p>If you are locked out, open a ticket or call STX (340) 774-0100 or STTJ (340) 775-2250.</p>',
 'password,reset,locked out,forgot', '', @now, @now),
(@cat_tickets, 1, 'How do I open a support ticket?',
 '<p>Click <b>Open a New Ticket</b> on the help desk home page, choose the help topic that best matches your issue, fill in the details, and submit. You will receive a ticket number by email to track progress.</p>',
 'open ticket,new ticket,submit,request', '', @now, @now),
(@cat_tickets, 1, 'How do I check the status of my ticket?',
 '<p>Click <b>Check Ticket Status</b> at the top of the page and sign in, or use the ticket number and email from your confirmation message. You will see all updates and can reply directly.</p>',
 'status,track,ticket number,check', '', @now, @now),
(@cat_tickets, 1, 'How long does it take to get a response?',
 '<p>Most tickets receive a first response within one business day. Urgent issues affecting a whole school or classroom are prioritized. You can add a reply to your ticket at any time to provide more information.</p>',
 'response time,sla,how long,wait', '', @now, @now),
(@cat_tutorials, 1, 'Tutorial: Setting up arahOS email on your phone',
 '<p><b>Step-by-step:</b></p><ol><li>Open your mail app and choose <b>Add Account &rarr; Exchange / Office 365</b>.</li><li>Enter your full arahOS email address (firstname.lastname@k12.vi).</li><li>Enter your arahOS network password.</li><li>Accept the security prompts and allow sync.</li><li>Your mail, calendar, and contacts will begin syncing.</li></ol><p>Trouble? Open a ticket with help topic <b>Email</b>.</p>',
 'email,mobile,phone,setup,exchange,office 365', '', @now, @now),
(@cat_tutorials, 1, 'Tutorial: Connecting to the arahOS Wi-Fi network',
 '<p><b>Step-by-step:</b></p><ol><li>Open Wi-Fi settings on your device.</li><li>Select the <b>arahOS-Staff</b> network.</li><li>Enter your arahOS username and password when prompted.</li><li>Accept the certificate if asked.</li></ol><p>For classroom or student devices, contact your site technician or open a ticket with help topic <b>Network</b>.</p>',
 'wifi,wireless,network,connect,internet', '', @now, @now);
